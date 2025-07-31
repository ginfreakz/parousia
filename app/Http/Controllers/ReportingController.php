<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Exports\ContactsExport;
use App\Exports\RegionExport;
use App\Exports\SkalaExport;
use App\Exports\SdmReportExport;
use App\Exports\UserTrainingReportExport;
use Maatwebsite\Excel\Facades\Excel;

class ReportingController extends Controller
{
    public function index()
{
    $user = Auth::user();
    $lembagaId = $user->lembaga_id;
    $roleId = $user->role_id;
    $userRegionIds = json_decode($user->region_id, true);

    // if ($roleId == 7 || $roleId == 5) {
    //     // User can see all data
    //     $nameSql = "SELECT id, username, name, role_id, lembaga_id FROM users WHERE role_id IN (1, 2, 6)";
    //     $names = DB::select($nameSql);
    //     $lembagas = DB::table('lembaga')->get();
    // } elseif ($lembagaId != 1) {
    //     // User with lembaga_id = 2 can only see contacts with the same lembaga_id
    //     $nameSql = "SELECT id, username, name, role_id, lembaga_id FROM users WHERE role_id IN (1, 2, 6)
    //                 AND lembaga_id = $lembagaId";
    //     $names = DB::select($nameSql);
    //     $lembagas = DB::table('lembaga')->where('id', $user->lembaga_id)->get();
    // } elseif ($roleId == 3) {
    //     $nameSql = "SELECT id, username, name, role_id, lembaga_id 
    //         FROM users 
    //         WHERE role_id IN (1, 2, 6)
    //         AND lembaga_id = $lembagaId
    //         AND EXISTS (
    //             SELECT 1
    //             FROM jsonb_array_elements_text(
    //                 CASE 
    //                     WHEN jsonb_typeof(users.region_id::jsonb) = 'array' 
    //                     THEN users.region_id::jsonb
    //                     ELSE '[]'::jsonb
    //                 END
    //             ) AS region
    //             WHERE region::text = ANY(ARRAY[" . implode(',', array_map(fn($id) => "'$id'", $userRegionIds)) . "])
    //         )";
    //     $names = DB::select($nameSql);
    //     $lembagas = DB::table('lembaga')->where('id', $user->lembaga_id)->get();
    // } else{
    //     // User with role_id in [1, 2] can only see contacts created by themselves
    //     $nameSql = "SELECT id, username, name, role_id, lembaga_id FROM users WHERE id = $user->id";
    //     $names = DB::select($nameSql);
    //     $lembagas = DB::table('lembaga')->where('id', $user->lembaga_id)->get();
    // }

    if ($roleId == 7 || $roleId == 5) {
        $nameSql = "SELECT id, username, name, role_id, lembaga_id FROM users WHERE role_id IN (1, 2, 6)";
        $names = DB::select($nameSql);
        $lembagas = DB::table('lembaga')->get();
    } elseif ($roleId == 3) {
        $regionConditions = implode(',', array_map(fn($id) => "'$id'", $userRegionIds));
        $nameSql = "
            SELECT id, username, name, role_id, lembaga_id 
            FROM users 
            WHERE role_id IN (1, 2, 6)
            AND EXISTS (
                SELECT 1
                FROM jsonb_array_elements_text(
                    CASE 
                        WHEN jsonb_typeof(users.region_id::jsonb) = 'array' 
                        THEN users.region_id::jsonb
                        ELSE '[]'::jsonb
                    END
                ) AS region
                WHERE region::text IN ($regionConditions)
            )";
        $names = DB::select($nameSql);
        $lembagas = DB::table('lembaga')->where('id', $user->lembaga_id)->get();
    } else {
        $nameSql = "SELECT id, username, name, role_id, lembaga_id FROM users WHERE id = $user->id";
        $names = DB::select($nameSql);
        $lembagas = DB::table('lembaga')->where('id', $user->lembaga_id)->get();
    }
    
    // Apply lembaga filter unless role is 7 or 5
    if ($roleId != 7 && $roleId != 5 && $lembagaId != 1) {
        $nameSql = rtrim($nameSql, ";") . " AND lembaga_id = $lembagaId";
        $names = DB::select($nameSql);
    }
    

    return view('report.contact-list', compact('lembagas', 'names'));
}

    // public function showFilteredDataContacts(Request $request)
    // {
        
    //     $lembaga = $request->filterLembaga;
    //     $name = $request->filterName;
    //     //$region = $request->filterRegion;
    //     $startDate = $request->startDate;
    //     $endDate = $request->endDate;

    //     // Generate dynamic month columns
    //     $months = [];
    //     $start = Carbon::parse($startDate)->startOfMonth();
    //     $end = Carbon::parse($endDate)->endOfMonth();
    //     while ($start->lte($end)) {
    //         $months[] = $start->format('M-y'); // Ex: Apr-24
    //         $start->addMonth();
    //     }

    //     // Base query
    //     $query = "SELECT contacts.utusan, ";
    //     $bindings = [];

    //     foreach ($months as $month) {
    //         $safeMonthKey = str_replace('-', '_', $month);
    //         $query .= "SUM(CASE WHEN to_char(contacts.tanggal, 'Mon-YY') = :month_{$safeMonthKey} THEN 1 ELSE 0 END) AS \"{$month}\", ";
    //         $bindings["month_{$safeMonthKey}"] = $month;
    //     }

    //     $query .= "COUNT(*) AS total FROM contacts 
    //            JOIN users ON contacts.utusan_id = users.id WHERE 1=1 ";

    //     $user = Auth::user();
    //     $lembagaId = $user->lembaga_id;
    //     $roleId = $user->role_id;
    //     $userRegionIds = json_decode($user->region_id, true);
    
    //     // if ($roleId == 7 || $roleId == 5) {
    //     //     // User can see all data
    //     //     $nameSql = "SELECT id, username, name, role_id, lembaga_id FROM users WHERE role_id IN (1, 2, 6)";
    //     //     $names = DB::select($nameSql);
    //     //     $lembagas = DB::table('lembaga')->get();
    //     // } elseif ($lembagaId != 1) {
    //     //     // User with lembaga_id = 2 can only see contacts with the same lembaga_id
    //     //     $nameSql = "SELECT id, username, name, role_id, lembaga_id FROM users WHERE role_id IN (1, 2, 6)
    //     //                 AND lembaga_id = $lembagaId";
    //     //     $names = DB::select($nameSql);
    //     //     $lembagas = DB::table('lembaga')->where('id', $user->lembaga_id)->get();
    //     //     $query .= " AND contacts.lembaga_id = " . $lembagaId;
    //     // } elseif ($roleId == 3) {
    //     //     $nameSql = "SELECT id, username, name, role_id, lembaga_id 
    //     //         FROM users 
    //     //         WHERE role_id IN (1, 2, 6)
    //     //         AND lembaga_id = $lembagaId
    //     //         AND EXISTS (
    //     //             SELECT 1
    //     //             FROM jsonb_array_elements_text(
    //     //                 CASE 
    //     //                     WHEN jsonb_typeof(users.region_id::jsonb) = 'array' 
    //     //                     THEN users.region_id::jsonb
    //     //                     ELSE '[]'::jsonb
    //     //                 END
    //     //             ) AS region
    //     //             WHERE region::text = ANY(ARRAY[" . implode(',', array_map(fn($id) => "'$id'", $userRegionIds)) . "])
    //     //         )";
    //     //     $names = DB::select($nameSql);
    //     //     $lembagas = DB::table('lembaga')->where('id', $user->lembaga_id)->get();
    //     //     $query .= " AND contacts.region_id IN (" . implode(',', array_fill(0, count($userRegionIds), '?')) . ")";
    //     // } else{
    //     //     // User with role_id in [1, 2] can only see contacts created by themselves
    //     //     $nameSql = "SELECT id, username, name, role_id, lembaga_id FROM users WHERE id = $user->id";
    //     //     $names = DB::select($nameSql);
    //     //     $lembagas = DB::table('lembaga')->where('id', $user->lembaga_id)->get();
    //     //     $query .= " AND contacts.utusan_id = " . $user->id;
    //     // }

    //     // Start role-based filtering
    //     if ($roleId == 7 || $roleId == 5) {
    //         $nameSql = "SELECT id, username, name, role_id, lembaga_id FROM users WHERE role_id IN (1, 2, 6)";
    //         $names = DB::select($nameSql);
    //         $lembagas = DB::table('lembaga')->get();
    //     } elseif ($roleId == 3) {
    //         $regionConditions = implode(',', array_map(fn($id) => "'$id'", $userRegionIds));
    //         $nameSql = "
    //             SELECT id, username, name, role_id, lembaga_id 
    //             FROM users 
    //             WHERE role_id IN (1, 2, 6)
    //             AND EXISTS (
    //                 SELECT 1
    //                 FROM jsonb_array_elements_text(
    //                     CASE 
    //                         WHEN jsonb_typeof(users.region_id::jsonb) = 'array' 
    //                         THEN users.region_id::jsonb
    //                         ELSE '[]'::jsonb
    //                     END
    //                 ) AS region
    //                 WHERE region::text IN ($regionConditions)
    //             )";
    //         $names = DB::select($nameSql);
    //         $lembagas = DB::table('lembaga')->where('id', $user->lembaga_id)->get();
    //         $query .= " AND contacts.region_id IN (" . implode(',', array_fill(0, count($userRegionIds), '?')) . ")";
    //     } else {
    //         $nameSql = "SELECT id, username, name, role_id, lembaga_id FROM users WHERE id = $user->id";
    //         $names = DB::select($nameSql);
    //         $lembagas = DB::table('lembaga')->where('id', $user->lembaga_id)->get();
    //         $query .= " AND contacts.utusan_id = " . $user->id;
    //     }

    //     // Apply lembaga_id filter after role logic
    //     if ($roleId != 7 && $roleId != 5 && $lembagaId != 1) {
    //         $query .= " AND contacts.lembaga_id = $lembagaId";
    //     }


    //     if ($lembaga && $lembaga != '0') {
    //         $query .= " AND users.lembaga_id = :lembaga";
    //         $bindings['lembaga'] = $lembaga;
    //     }
    //     if ($name && $name != '0') {
    //         $query .= " AND contacts.utusan_id = :name";
    //         $bindings['name'] = $name;
    //     }
    //     if ($startDate) {
    //         $query .= " AND contacts.tanggal >= :startDate";
    //         $bindings['startDate'] = Carbon::parse($startDate)->startOfMonth()->toDateString();
    //     }
    //     if ($endDate) {
    //         $query .= " AND contacts.tanggal <= :endDate";
    //         $bindings['endDate'] = Carbon::parse($endDate)->endOfMonth()->toDateString();
    //     }

    //     $query .= " AND contacts.status = 1
    //                 GROUP BY contacts.utusan";
    //     echo $query;
    //     // Execute Query
    //     $contacts = DB::select($query, $bindings);

    //     // Calculate Percentage
    //     $totalSum = array_sum(array_column($contacts, 'total'));
    //     foreach ($contacts as $contact) {
    //         $contact->percentage = $totalSum > 0 ? ($contact->total / $totalSum) * 100 : 0;
    //     }

    //     return view('report.contact-list', compact('lembagas', 'names', 'contacts', 'months', 'totalSum'));
    // }

    public function showFilteredDataContacts(Request $request)
{
    $lembaga = $request->filterLembaga;
    $name = $request->filterName;
    $startDate = $request->startDate;
    $endDate = $request->endDate;

    $months = [];
    $start = Carbon::parse($startDate)->startOfMonth();
    $end = Carbon::parse($endDate)->endOfMonth();
    while ($start->lte($end)) {
        $months[] = $start->format('M-y');
        $start->addMonth();
    }

    $query = "SELECT users.name AS utusan, ";
    $bindings = [];

    foreach ($months as $month) {
        $safeMonthKey = str_replace('-', '_', $month);
        $query .= "SUM(CASE WHEN to_char(activity.tanggal, 'Mon-YY') = :month_{$safeMonthKey} THEN 1 ELSE 0 END) AS \"{$month}\", ";
        $bindings["month_{$safeMonthKey}"] = $month;
    }

    $query .= "COUNT(*) AS total
               FROM (
                   SELECT DISTINCT ON (contact_id, to_char(tanggal, 'YYYY-MM')) contact_id, tanggal
                   FROM contacts_history
               ) AS activity
               JOIN contacts ON contacts.id = activity.contact_id
               JOIN users ON contacts.utusan_id = users.id
               WHERE 1=1 ";

    $user = Auth::user();
    $lembagaId = $user->lembaga_id;
    $roleId = $user->role_id;
    $userRegionIds = json_decode($user->region_id, true);

    if ($roleId == 7 || $roleId == 5) {
        $nameSql = "SELECT id, username, name, role_id, lembaga_id FROM users WHERE role_id IN (1, 2, 6)";
        $names = DB::select($nameSql);
        $lembagas = DB::table('lembaga')->get();
    } elseif ($roleId == 3) {
        $regionConditions = implode(',', array_map(fn($id) => "'$id'", $userRegionIds));
        $nameSql = "
            SELECT id, username, name, role_id, lembaga_id 
            FROM users 
            WHERE role_id IN (1, 2, 6)
            AND EXISTS (
                SELECT 1
                FROM jsonb_array_elements_text(
                    CASE 
                        WHEN jsonb_typeof(users.region_id::jsonb) = 'array' 
                        THEN users.region_id::jsonb
                        ELSE '[]'::jsonb
                    END
                ) AS region
                WHERE region::text IN ($regionConditions)
            )";
        $names = DB::select($nameSql);
        $lembagas = DB::table('lembaga')->where('id', $user->lembaga_id)->get();
        $query .= " AND contacts.region_id IN (" . implode(',', array_fill(0, count($userRegionIds), '?')) . ")";
    } else {
        $nameSql = "SELECT id, username, name, role_id, lembaga_id FROM users WHERE id = $user->id";
        $names = DB::select($nameSql);
        $lembagas = DB::table('lembaga')->where('id', $user->lembaga_id)->get();
        $query .= " AND contacts.utusan_id = " . $user->id;
    }

    if ($roleId != 7 && $roleId != 5 && $lembagaId != 1) {
        $query .= " AND contacts.lembaga_id = $lembagaId";
    }

    if ($lembaga && $lembaga != '0') {
        $query .= " AND users.lembaga_id = :lembaga";
        $bindings['lembaga'] = $lembaga;
    }
    if ($name && $name != '0') {
        $query .= " AND contacts.utusan_id = :name";
        $bindings['name'] = $name;
    }
    if ($startDate) {
        $query .= " AND activity.tanggal >= :startDate";
        $bindings['startDate'] = Carbon::parse($startDate)->startOfMonth()->toDateString();
    }
    if ($endDate) {
        $query .= " AND activity.tanggal <= :endDate";
        $bindings['endDate'] = Carbon::parse($endDate)->endOfMonth()->toDateString();
    }

    $query .= " GROUP BY users.name";
    //echo $query; // Debugging line to see the generated SQL query
    $contacts = DB::select($query, $bindings);

    $totalSum = array_sum(array_column($contacts, 'total'));    
    foreach ($contacts as $contact) {
        $contact->percentage = $totalSum > 0 ? ($contact->total / $totalSum) * 100 : 0;
    }

    return view('report.contact-list', compact('lembagas', 'names', 'contacts', 'months', 'totalSum'));
}

    public function skalaReport()
    {
        $user = Auth::user();
        $lembagaId = $user->lembaga_id;
        $roleId = $user->role_id;
        $userRegionIds = json_decode($user->region_id, true);

        // if ($roleId == 7 || $roleId == 5) {
        //     // User can see all data
        //     $regionSql = "SELECT * FROM regions";
        //     $regions = DB::select($regionSql);
        //     $lembagas = DB::table('lembaga')->get();
        // } elseif ($lembagaId != 1) {
        //     // User with lembaga_id = 2 can only see contacts with the same lembaga_id
        //     $regionSql = "SELECT * FROM regions WHERE id IN (" . implode(',', $userRegionIds) . ")";
        //     $regions = DB::select($regionSql);
        //     $lembagas = DB::table('lembaga')->where('id', $user->lembaga_id)->get();
        // } elseif ($roleId == 3) {
        //     // User with role_id = 3 can see contacts in the same region.provinsi
        //     $userRegions = DB::table('regions')->whereIn('id', $userRegionIds)->pluck('provinsi')->unique();
        //     // Convert $userRegions to a comma-separated string for raw SQL
        //     $provinsiList = "'" . implode("','", $userRegions->toArray()) . "'";
            
        //     $regionSql = "SELECT * FROM regions WHERE provinsi IN ($provinsiList)";
            
        //     $regions = DB::select($regionSql);
        //     $lembagas = DB::table('lembaga')->where('id', $user->lembaga_id)->get();
        // } else{
        //     // User with role_id in [1, 2] can only see contacts created by themselves
        //     $userKotas = DB::table('regions')->whereIn('id', $userRegionIds)->pluck('kota')->unique();
        //     $regionSql = "SELECT * FROM regions WHERE id IN (" . implode(',', $userRegionIds) . ")";
        //     $regions = DB::select($regionSql);
        //     $lembagas = DB::table('lembaga')->where('id', $user->lembaga_id)->get();
        // }

        if ($roleId == 7 || $roleId == 5) {
            $regionSql = "SELECT * FROM regions";
            $regions = DB::select($regionSql);
        } elseif ($roleId == 3) {
            // $userRegions = DB::table('regions')->whereIn('id', $userRegionIds)->pluck('provinsi')->unique();
            // $provinsiList = "'" . implode("','", $userRegions->toArray()) . "'";
            // $regionSql = "SELECT * FROM regions WHERE provinsi IN ($provinsiList)";
            // $regions = DB::select($regionSql);

            $userKotas = DB::table('regions')->whereIn('id', $userRegionIds)->pluck('kota')->unique();
            $regionSql = "SELECT * FROM regions WHERE id IN (" . implode(',', $userRegionIds) . ")";
            $regions = DB::select($regionSql);
        } else {
            $userKotas = DB::table('regions')->whereIn('id', $userRegionIds)->pluck('kota')->unique();
            $regionSql = "SELECT * FROM regions WHERE id IN (" . implode(',', $userRegionIds) . ")";
            $regions = DB::select($regionSql);
        }
        
        // Centralized lembaga fetching
        $lembagas = ($roleId == 7 || $roleId == 5)
            ? DB::table('lembaga')->get()
            : DB::table('lembaga')->where('id', $user->lembaga_id)->get();
        

        return view('report.contact-skala', compact('lembagas', 'regions'));
    }

    public function filteredSkalaReport(Request $request)
{
    // Get filter values
    $startDate = $request->input('startDate');
    $endDate = $request->input('endDate');
    $region = $request->input('filterRegion');
    $lembaga = $request->input('filterLembaga');
    $skala = $request->input('filterSkala');

    // Default filter values if empty
    if (!$startDate) {
        $startDate = Carbon::now()->subMonths(3)->format('Y-m'); // Default: last 3 months
    }
    if (!$endDate) {
        $endDate = Carbon::now()->format('Y-m');
    }

    // SQL Query to count each contact **only once per month under their highest skala**
    // $query = "
    //     WITH RankedContacts AS (
    //         SELECT 
    //             ch.contact_id,
    //             ch.skala,
    //             TO_CHAR(ch.tanggal, 'Mon-YY') AS month,
    //             ROW_NUMBER() OVER (PARTITION BY ch.contact_id, TO_CHAR(ch.tanggal, 'Mon-YY') ORDER BY ch.skala DESC) AS rank
    //         FROM contacts_history ch
    //         JOIN users u ON ch.utusan = u.name
    //         JOIN contacts c ON ch.contact_id = c.id
    //         JOIN regions r ON c.region_id = r.id
    //         WHERE c.status = 1
    //         AND TO_CHAR(ch.tanggal, 'YYYY-MM') BETWEEN :startDate AND :endDate
    //         AND ch.skala IN (-3,-2,-1,0,1,2,3)
    // ";

    $query="WITH RankedContacts AS (
            SELECT 
                ch.contact_id,
                ch.skala,
                TO_CHAR(ch.tanggal, 'Mon-YY') AS month,
                ROW_NUMBER() OVER (
    PARTITION BY ch.contact_id, TO_CHAR(ch.tanggal, 'YYYY-MM') 
    ORDER BY ch.tanggal DESC, ch.skala DESC
    )
    AS rank
            FROM contacts_history ch
            JOIN users u ON ch.utusan = u.name
            JOIN contacts c ON ch.contact_id = c.id
            JOIN regions r ON c.region_id = r.id
            WHERE c.status = 1
            AND TO_CHAR(ch.tanggal, 'YYYY-MM') BETWEEN :startDate AND :endDate
            AND ch.skala IN (-3,-2,-1,0,1,2,3)";
    
    $bindings = [
        'startDate' => Carbon::parse($startDate)->format('Y-m'),
        'endDate' => Carbon::parse($endDate)->format('Y-m')
    ];

    $user = Auth::user();
    $lembagaId = $user->lembaga_id;
    $roleId = $user->role_id;
    $userRegionIds = json_decode($user->region_id, true);

    // if ($roleId == 7 || $roleId == 5) {
    //     // User can see all data
    //     $regionSql = "SELECT * FROM regions";
    //     $regions = DB::select($regionSql);
    //     $lembagas = DB::table('lembaga')->get();
    // } elseif ($lembagaId != 1) {
    //     // User with lembaga_id = 2 can only see contacts with the same lembaga_id
    //     $regionSql = "SELECT * FROM regions WHERE id IN (" . implode(',', $userRegionIds) . ")";
    //     $regions = DB::select($regionSql);
    //     $lembagas = DB::table('lembaga')->where('id', $user->lembaga_id)->get();
    //     $query .= " AND u.lembaga_id = :lembaga";
    // } elseif ($roleId == 3) {
    //     // User with role_id = 3 can see contacts in the same region.provinsi
    //     $userRegions = DB::table('regions')->whereIn('id', $userRegionIds)->pluck('provinsi')->unique();
    //     // Convert $userRegions to a comma-separated string for raw SQL
    //     $provinsiList = "'" . implode("','", $userRegions->toArray()) . "'";
        
    //     $regionSql = "SELECT * FROM regions WHERE provinsi IN ($provinsiList)";
        
    //     $regions = DB::select($regionSql);
    //     $lembagas = DB::table('lembaga')->where('id', $user->lembaga_id)->get();

    //     $query .= " AND provinsi IN ($provinsiList)";
    // } else{
    //     // User with role_id in [1, 2] can only see contacts created by themselves
    //     $userKotas = DB::table('regions')->whereIn('id', $userRegionIds)->pluck('kota')->unique();
    //     $regionSql = "SELECT * FROM regions WHERE id IN (" . implode(',', $userRegionIds) . ")";
    //     $regions = DB::select($regionSql);
    //     $lembagas = DB::table('lembaga')->where('id', $user->lembaga_id)->get();

    //     $query .= " AND c.utusan_id = " . $user->id;
    // }

    if ($roleId == 7 || $roleId == 5) {
        $regionSql = "SELECT * FROM regions";
        $regions = DB::select($regionSql);
        $query .= " "; // no additional filter
    } elseif ($roleId == 3) {
        // $userRegions = DB::table('regions')->whereIn('id', $userRegionIds)->pluck('provinsi')->unique();
        // $provinsiList = "'" . implode("','", $userRegions->toArray()) . "'";
        // $regionSql = "SELECT * FROM regions WHERE provinsi IN ($provinsiList)";
        // $regions = DB::select($regionSql);
        // $query .= " AND provinsi IN ($provinsiList)";

        $userKotas = DB::table('regions')->whereIn('id', $userRegionIds)->pluck('kota')->unique();
        $regionSql = "SELECT * FROM regions WHERE id IN (" . implode(',', $userRegionIds) . ")";
        $regions = DB::select($regionSql);
    } else {
        $userKotas = DB::table('regions')->whereIn('id', $userRegionIds)->pluck('kota')->unique();
        $regionSql = "SELECT * FROM regions WHERE id IN (" . implode(',', $userRegionIds) . ")";
        $regions = DB::select($regionSql);
        $query .= " AND c.utusan_id = " . $user->id;
    }
    
    // Apply lembaga_id filter (except for full-access roles)
    if ($roleId != 7 && $roleId != 5 && $lembagaId != 1) {
        $query .= " AND u.lembaga_id = $lembagaId";
    }
    
    // Single lembaga query
    $lembagas = ($roleId == 7 || $roleId == 5)
        ? DB::table('lembaga')->get()
        : DB::table('lembaga')->where('id', $user->lembaga_id)->get();
    

    if ($region && !in_array('0', $region)) {
        // Create placeholders for named parameters
        $regionPlaceholders = array_map(function($index) {
            return ':region' . $index;
        }, array_keys($region));
    
        // Append to the query using named parameters
        $query .= " AND c.region_id IN (" . implode(',', $regionPlaceholders) . ")";
    
        // Add regions to the bindings array with corresponding keys
        foreach ($region as $index => $regionId) {
            $bindings['region' . $index] = $regionId;
        }
    }

    if ($lembaga && $lembaga != '0') {
        //$query .= " AND ch.utusan = :lembaga";
        $query .= " AND u.lembaga_id = :lembaga";
        $bindings['lembaga'] = $lembaga;
    }

    if ($skala !== null && $skala !== '') {
        $query .= " AND ch.skala = :skala";
        $bindings['skala'] = $skala;
    }

    $query .= "
        ),
        FilteredContacts AS (
            SELECT contact_id, skala, month
            FROM RankedContacts
            WHERE rank = 1
        ),
        FinalCounts AS (
            SELECT 
                fc.skala, 
                fc.month, 
                COUNT(fc.contact_id) AS total
            FROM FilteredContacts fc
            GROUP BY fc.skala, fc.month
        )
        SELECT * FROM FinalCounts
        ORDER BY skala ASC
    ";
    //echo $query; // Debugging line to see the generated SQL query
    // Execute query
    $rawContacts = DB::select($query, $bindings);

    // Convert results into an associative array
    $contacts = [];
    $totalSum = 0;
    $monthlyCounts = [];

    foreach ($rawContacts as $row) {
        $skala = $row->skala;
        $month = $row->month;
        $total = $row->total;

        if (!isset($contacts[$skala])) {
            $contacts[$skala] = [
                'skala' => $skala,
                'monthly_counts' => [],
                'total' => 0
            ];
        }

        $contacts[$skala]['monthly_counts'][$month] = $total;
        $contacts[$skala]['total'] += $total;
        $totalSum += $total;
        $monthlyCounts[$month] = ($monthlyCounts[$month] ?? 0) + $total;
    }

    // Calculate percentage
    foreach ($contacts as &$contact) {
        $contact['percentage'] = $totalSum > 0 ? ($contact['total'] / $totalSum) * 100 : 0;
    }

    return view('report.contact-skala', compact(
        'contacts', 'totalSum', 'lembagas', 'regions'
    ));
}

    public function regionReport()
    {
        $user = Auth::user();
        $lembagaId = $user->lembaga_id;
        $roleId = $user->role_id;
        $userRegionIds = json_decode($user->region_id, true);

        // if ($roleId == 7 || $roleId == 5) {
        //     // User can see all data
        //     $regionSql = "SELECT * FROM regions";
        //     $regions = DB::select($regionSql);
        //     $lembagas = DB::table('lembaga')->get();
        // } elseif ($lembagaId != 1) {
        //     // User with lembaga_id = 2 can only see contacts with the same lembaga_id
        //     $regionSql = "SELECT * FROM regions WHERE id IN (" . implode(',', $userRegionIds) . ")";
        //     $regions = DB::select($regionSql);
        //     $lembagas = DB::table('lembaga')->where('id', $user->lembaga_id)->get();
        // } elseif ($roleId == 3) {
        //     // User with role_id = 3 can see contacts in the same region.provinsi
        //     $userRegions = DB::table('regions')->whereIn('id', $userRegionIds)->pluck('provinsi')->unique();
        //     // Convert $userRegions to a comma-separated string for raw SQL
        //     $provinsiList = "'" . implode("','", $userRegions->toArray()) . "'";
            
        //     $regionSql = "SELECT * FROM regions WHERE provinsi IN ($provinsiList)";
            
        //     $regions = DB::select($regionSql);
        //     $lembagas = DB::table('lembaga')->where('id', $user->lembaga_id)->get();
        // } else{
        //     // User with role_id in [1, 2] can only see contacts created by themselves
        //     $userKotas = DB::table('regions')->whereIn('id', $userRegionIds)->pluck('kota')->unique();
        //     $regionSql = "SELECT * FROM regions WHERE id IN (" . implode(',', $userRegionIds) . ")";
        //     $regions = DB::select($regionSql);
        //     $lembagas = DB::table('lembaga')->where('id', $user->lembaga_id)->get();
        // }

        if ($roleId == 7 || $roleId == 5) {
            // Full access
            $regionSql = "SELECT * FROM regions";
            $regions = DB::select($regionSql);
        } elseif ($roleId == 3) {
            // Regions by provinsi
            // $userRegions = DB::table('regions')->whereIn('id', $userRegionIds)->pluck('provinsi')->unique();
            // $provinsiList = "'" . implode("','", $userRegions->toArray()) . "'";
            // $regionSql = "SELECT * FROM regions WHERE provinsi IN ($provinsiList)";
            // $regions = DB::select($regionSql);

            $regionSql = "SELECT * FROM regions WHERE id IN (" . implode(',', $userRegionIds) . ")";
            $regions = DB::select($regionSql);
        } else {
            // Default: region by id
            $regionSql = "SELECT * FROM regions WHERE id IN (" . implode(',', $userRegionIds) . ")";
            $regions = DB::select($regionSql);
        }
        
        // Centralized lembaga logic
        $lembagas = ($roleId == 7 || $roleId == 5)
            ? DB::table('lembaga')->get()
            : DB::table('lembaga')->where('id', $user->lembaga_id)->get();
        

        return view('report.contact-region', compact('lembagas', 'regions'));
    }

    public function filteredRegionReport(Request $request)
    {
        $user = Auth::user();
        $lembagaId = $user->lembaga_id;
        $roleId = $user->role_id;
        $userRegionIds = json_decode($user->region_id, true);
        
        $region = $request->filterRegion;
        $startDate = $request->startDate;
        $endDate = $request->endDate;
        $lembaga = $request->filterLembaga;

        // Generate month labels between start and end dates
        $months = [];
        $start = Carbon::parse($startDate)->startOfMonth();
        $end = Carbon::parse($endDate)->endOfMonth();
        while ($start <= $end) {
            $months[] = $start->format('M-Y');
            $start->addMonth();
        }

        // Dynamic SQL Query
        $query = "SELECT regions.kota";
        foreach ($months as $month) {
            $query .= ", SUM(CASE WHEN TO_CHAR(contacts.tanggal, 'Mon-YYYY') = '{$month}' THEN 1 ELSE 0 END) AS \"{$month}\"";
        }
        $query .= ", COUNT(*) AS total
               FROM contacts
               JOIN regions ON contacts.region_id = regions.id
               WHERE status = 1";

        // if ($roleId == 7 || $roleId == 5) {
        //     // User can see all data
        //     $regionSql = "SELECT * FROM regions";
        //     $regions = DB::select($regionSql);
        //     $lembagas = DB::table('lembaga')->get();
        // } elseif ($lembagaId != 1) {
        //     // User with lembaga_id = 2 can only see contacts with the same lembaga_id
        //     $regionSql = "SELECT * FROM regions WHERE id IN (" . implode(',', $userRegionIds) . ")";
        //     $regions = DB::select($regionSql);
        //     $lembagas = DB::table('lembaga')->where('id', $user->lembaga_id)->get();
        //     $query .= " AND contacts.lembaga_id = " . $lembagaId;
        // } elseif ($roleId == 3) {
        //     // User with role_id = 3 can see contacts in the same region.provinsi
        //     $userRegions = DB::table('regions')->whereIn('id', $userRegionIds)->pluck('provinsi')->unique();
        //     // Convert $userRegions to a comma-separated string for raw SQL
        //     $provinsiList = "'" . implode("','", $userRegions->toArray()) . "'";
            
        //     $regionSql = "SELECT * FROM regions WHERE provinsi IN ($provinsiList)";

        //     $regions = DB::select($regionSql);
        //     $lembagas = DB::table('lembaga')->where('id', $user->lembaga_id)->get();
            
        //     $query .= " AND provinsi IN ($provinsiList)";
        // } else{
        //     // User with role_id in [1, 2, 6] can only see contacts created by themselves
        //     $userKotas = DB::table('regions')->whereIn('id', $userRegionIds)->pluck('kota')->unique();
        //     $regionSql = "SELECT * FROM regions WHERE id IN (" . implode(',', $userRegionIds) . ")";
        //     $regions = DB::select($regionSql);
        //     $lembagas = DB::table('lembaga')->where('id', $user->lembaga_id)->get();
        //     $query .= " AND contacts.utusan_id = " . $user->id;
        // }

        // Role 7 or 5: full access
        if ($roleId == 7 || $roleId == 5) {
            $regionSql = "SELECT * FROM regions";
            $regions = DB::select($regionSql);
            $query .= " "; // No filters
        }

        // Role 3: filter by provinsi
        elseif ($roleId == 3) {
            // $userRegions = DB::table('regions')->whereIn('id', $userRegionIds)->pluck('provinsi')->unique();
            // $provinsiList = "'" . implode("','", $userRegions->toArray()) . "'";
            // $regionSql = "SELECT * FROM regions WHERE provinsi IN ($provinsiList)";
            // $regions = DB::select($regionSql);
            // $query .= " AND provinsi IN ($provinsiList)";

            $userKotas = DB::table('regions')->whereIn('id', $userRegionIds)->pluck('kota')->unique();
            $regionSql = "SELECT * FROM regions WHERE id IN (" . implode(',', $userRegionIds) . ")";
            $regions = DB::select($regionSql);
        }

        // Default: users with role 1, 2, or 6, filter by kota + user
        else {
            $userKotas = DB::table('regions')->whereIn('id', $userRegionIds)->pluck('kota')->unique();
            $regionSql = "SELECT * FROM regions WHERE id IN (" . implode(',', $userRegionIds) . ")";
            $regions = DB::select($regionSql);
            $query .= " AND contacts.utusan_id = " . $user->id;
        }

        // Apply lembaga filter at the end, except for admin roles
        if ($roleId != 7 && $roleId != 5 && $lembagaId != 1) {
            $query .= " AND contacts.lembaga_id = " . $lembagaId;
        }

        // Lembaga query (only once)
        $lembagas = ($roleId == 7 || $roleId == 5)
            ? DB::table('lembaga')->get()
            : DB::table('lembaga')->where('id', $user->lembaga_id)->get();

        // Bindings
        $bindings = [];
        if ($region && !in_array('0', $region)) {
            $query .= " AND contacts.region_id IN (" . implode(',', array_fill(0, count($region), '?')) . ")";
            $bindings = array_merge($bindings, $region);
        }
        if ($lembaga && $lembaga != '0') {
            $query .= " AND contacts.lembaga_id = ?";
            $bindings[] = $lembaga;
        }
        if ($startDate) {
            $query .= " AND contacts.tanggal >= ?";
            $bindings[] = Carbon::parse($startDate)->startOfMonth()->toDateString();
        }
        if ($endDate) {
            $query .= " AND contacts.tanggal <= ?";
            $bindings[] = Carbon::parse($endDate)->endOfMonth()->toDateString();
        }

        $query .= " GROUP BY regions.kota";

        // Execute the query
        $contacts = DB::select($query, $bindings);

        // Calculate totals & percentages
        $totalSum = array_sum(array_column($contacts, 'total'));

        if ($totalSum > 0) {
            foreach ($contacts as $contact) {
                $contact->percentage = ($contact->total / $totalSum) * 100;
            }
        } else {
            foreach ($contacts as $contact) {
                $contact->percentage = 0; // If no data, set percentage to 0%
            }
        }

        return view('report.contact-region', compact('regions', 'contacts', 'months', 'lembagas', 'totalSum'));
    }
    public function exportRegionReport(Request $request)
    {
        $region = $request->filterRegion;
        $startDate = $request->startDate;
        $endDate = $request->endDate;
        $lembaga = $request->filterLembaga;

        // Generate months between start and end dates
        $months = [];
        $start = Carbon::parse($startDate)->startOfMonth();
        $end = Carbon::parse($endDate)->endOfMonth();
        while ($start <= $end) {
            $months[] = $start->format('M-Y');
            $start->addMonth();
        }

        // Dynamic SQL Query
        $query = "SELECT regions.kota";
        foreach ($months as $month) {
            $query .= ", SUM(CASE WHEN TO_CHAR(contacts.tanggal, 'Mon-YYYY') = '{$month}' THEN 1 ELSE 0 END) AS \"{$month}\"";
        }
        $query .= ", COUNT(*) AS total
                FROM contacts
                JOIN regions ON contacts.region_id = regions.id
                WHERE status = 1";

        // Bindings
        $bindings = [];
        if ($region && !in_array('0', $region)) {
            $query .= " AND contacts.region_id IN (" . implode(',', array_fill(0, count($region), '?')) . ")";
            $bindings = array_merge($bindings, $region);
        }
        if ($lembaga && $lembaga != '0') {
            $query .= " AND contacts.lembaga_id = ?";
            $bindings[] = $lembaga;
        }
        if ($startDate) {
            $query .= " AND contacts.tanggal >= ?";
            $bindings[] = Carbon::parse($startDate)->startOfMonth()->toDateString();
        }
        if ($endDate) {
            $query .= " AND contacts.tanggal <= ?";
            $bindings[] = Carbon::parse($endDate)->endOfMonth()->toDateString();
        }

        $query .= " GROUP BY regions.kota";

        // Execute query
        $contacts = DB::select($query, $bindings);

        // Calculate total & percentages
        $totalSum = array_sum(array_column($contacts, 'total'));

        if ($totalSum > 0) {
            foreach ($contacts as $contact) {
                $contact->percentage = ($contact->total / $totalSum) * 100;
            }
        } else {
            foreach ($contacts as $contact) {
                $contact->percentage = 0; // If no data, set percentage to 0%
            }
        }

        // Convert to collection for Excel export
        $formattedData = collect($contacts)->map(function ($data, $index) use ($months) {
            $row = [
                'No' => $index + 1,
                'Lokasi' => $data->kota,
            ];

            foreach ($months as $month) {
                $row[$month] = $data->{$month} ?? 0;
            }

            $row['Total'] = $data->total;
            $row['Percentage'] = number_format($data->percentage, 2) . '%';

            return (object) $row;
        });

        return Excel::download(new RegionExport($formattedData, $months), 'Region_Report.xlsx');
    }
//     public function exportSkalaReport(Request $request)
// {
//     // Get filters
//     $startDate = $request->input('startDate');
//     $endDate = $request->input('endDate');
//     $region = $request->input('filterRegion');
//     $lembaga = $request->input('filterLembaga');
//     $skala = $request->input('filterSkala');

//     // Default dates
//     if (!$startDate) {
//         $startDate = Carbon::now()->subMonths(3)->format('Y-m');
//     }
//     if (!$endDate) {
//         $endDate = Carbon::now()->format('Y-m');
//     }

//     // SQL Query (Same as `filteredSkalaReport`)
//     $query = "
//         WITH RankedContacts AS (
//             SELECT 
//                 ch.contact_id,
//                 ch.skala,
//                 TO_CHAR(ch.created_at, 'Mon-YY') AS month,
//                 ROW_NUMBER() OVER (PARTITION BY ch.contact_id, TO_CHAR(ch.created_at, 'Mon-YY') ORDER BY ch.skala DESC) AS rank
//             FROM contacts_history ch
//             WHERE TO_CHAR(ch.created_at, 'YYYY-MM') BETWEEN :startDate AND :endDate
//             AND ch.skala IN (-3,-2,-1,0,1,2,3)
//     ";

//     $bindings = [
//         'startDate' => Carbon::parse($startDate)->format('Y-m'),
//         'endDate' => Carbon::parse($endDate)->format('Y-m')
//     ];

//     // Apply filters
//     if ($region && $region != '0') {
//         $query .= " AND ch.region_id = :region";
//         $bindings['region'] = $region;
//     }

//     if ($lembaga && $lembaga != '0') {
//         $query .= " AND ch.utusan = :lembaga";
//         $bindings['lembaga'] = $lembaga;
//     }

//     if ($skala !== null && $skala !== '') {
//         $query .= " AND ch.skala = :skala";
//         $bindings['skala'] = $skala;
//     }

//     $query .= "
//         ),
//         FilteredContacts AS (
//             SELECT contact_id, skala, month
//             FROM RankedContacts
//             WHERE rank = 1
//         ),
//         FinalCounts AS (
//             SELECT 
//                 fc.skala, 
//                 fc.month, 
//                 COUNT(fc.contact_id) AS total
//             FROM FilteredContacts fc
//             GROUP BY fc.skala, fc.month
//         )
//         SELECT * FROM FinalCounts
//         ORDER BY skala ASC
//     ";

//     // Execute query
//     $rawContacts = DB::select($query, $bindings);

//     // Format data
//     $contacts = [];
//     $totalSum = 0;
//     $monthlyCounts = [];
//     $months = collect($rawContacts)->pluck('month')->unique()->sort()->values()->all();

//     foreach ($rawContacts as $row) {
//         $skala = $row->skala;
//         $month = $row->month;
//         $total = $row->total;

//         if (!isset($contacts[$skala])) {
//             $contacts[$skala] = [
//                 'skala' => $skala,
//                 'monthly_counts' => [],
//                 'total' => 0
//             ];
//         }

//         $contacts[$skala]['monthly_counts'][$month] = $total;
//         $contacts[$skala]['total'] += $total;
//         $totalSum += $total;
//         $monthlyCounts[$month] = ($monthlyCounts[$month] ?? 0) + $total;
//     }

//     // Calculate percentages
//     foreach ($contacts as &$contact) {
//         $contact['percentage'] = $totalSum > 0 ? ($contact['total'] / $totalSum) * 100 : 0;
//     }

//     // Convert to collection for export
//     $formattedData = collect($contacts)->map(function ($data, $index) use ($months) {
//         $row = [
//             'No' => $index + 1,
//             'skala' => $data['skala'],
//         ];

//         foreach ($months as $month) {
//             $row[$month] = $data['monthly_counts'][$month] ?? 0;
//         }

//         $row['total'] = $data['total'];
//         $row['percentage'] = number_format($data['percentage'], 2) . '%';

//         return (object) $row;
//     });

//     return Excel::download(new SkalaExport($formattedData, $months), 'Skala_Report.xlsx');
// }

public function exportSkalaReport(Request $request)
{
    $startDate = $request->input('startDate') ?? Carbon::now()->subMonths(3)->format('Y-m');
    $endDate = $request->input('endDate') ?? Carbon::now()->format('Y-m');
    $region = $request->input('filterRegion');
    $lembaga = $request->input('filterLembaga');
    $skala = $request->input('filterSkala');

    $bindings = [
        'startDate' => Carbon::parse($startDate)->format('Y-m'),
        'endDate' => Carbon::parse($endDate)->format('Y-m')
    ];

    $user = Auth::user();
    $lembagaId = $user->lembaga_id;
    $roleId = $user->role_id;
    $userRegionIds = json_decode($user->region_id, true);

    // $query = "
    //     WITH RankedContacts AS (
    //         SELECT 
    //             ch.contact_id,
    //             ch.skala,
    //             TO_CHAR(ch.tanggal, 'Mon-YY') AS month,
    //             ROW_NUMBER() OVER (PARTITION BY ch.contact_id, TO_CHAR(ch.tanggal, 'Mon-YY') ORDER BY ch.skala DESC) AS rank
    //         FROM contacts_history ch
    //         JOIN users u ON ch.utusan = u.name
    //         JOIN contacts c ON ch.contact_id = c.id
    //         JOIN regions r ON c.region_id = r.id
    //         WHERE c.status = 1
    //         AND TO_CHAR(ch.tanggal, 'YYYY-MM') BETWEEN :startDate AND :endDate
    //         AND ch.skala IN (-3,-2,-1,0,1,2,3)
    // ";

    $query="WITH RankedContacts AS (
            SELECT 
                ch.contact_id,
                ch.skala,
                TO_CHAR(ch.tanggal, 'Mon-YY') AS month,
                ROW_NUMBER() OVER (
    PARTITION BY ch.contact_id, TO_CHAR(ch.tanggal, 'YYYY-MM') 
    ORDER BY ch.tanggal DESC, ch.skala DESC
    )
    AS rank
            FROM contacts_history ch
            JOIN users u ON ch.utusan = u.name
            JOIN contacts c ON ch.contact_id = c.id
            JOIN regions r ON c.region_id = r.id
            WHERE c.status = 1
            AND TO_CHAR(ch.tanggal, 'YYYY-MM') BETWEEN :startDate AND :endDate
            AND ch.skala IN (-3,-2,-1,0,1,2,3)";

    if ($roleId == 7 || $roleId == 5) {
        // All data access — no additional condition
    } elseif ($lembagaId != 1) {
        $query .= " AND u.lembaga_id = :userLembaga";
        $bindings['userLembaga'] = $lembagaId;
    } elseif ($roleId == 3) {
        // $userRegions = DB::table('regions')->whereIn('id', $userRegionIds)->pluck('provinsi')->unique();
        // $provinsiList = "'" . implode("','", $userRegions->toArray()) . "'";
        // $query .= " AND provinsi IN ($provinsiList)";

        $query .= " AND c.region_id IN (" . implode(',', $userRegionIds) . ")";
    } else {
        $query .= " AND c.utusan_id = " . $user->id;
    }

    if ($region && !in_array('0', $region)) {
        $regionPlaceholders = array_map(fn($i) => ":region$i", array_keys($region));
        $query .= " AND c.region_id IN (" . implode(',', $regionPlaceholders) . ")";
        foreach ($region as $index => $regionId) {
            $bindings["region$index"] = $regionId;
        }
    }

    if ($lembaga && $lembaga != '0') {
        $query .= " AND u.lembaga_id = :lembaga";
        $bindings['lembaga'] = $lembaga;
    }

    if ($skala !== null && $skala !== '') {
        $query .= " AND ch.skala = :skala";
        $bindings['skala'] = $skala;
    }

    $query .= "
        ),
        FilteredContacts AS (
            SELECT contact_id, skala, month
            FROM RankedContacts
            WHERE rank = 1
        ),
        FinalCounts AS (
            SELECT 
                fc.skala, 
                fc.month, 
                COUNT(fc.contact_id) AS total
            FROM FilteredContacts fc
            GROUP BY fc.skala, fc.month
        )
        SELECT * FROM FinalCounts
        ORDER BY skala ASC
    ";

    $rawContacts = DB::select($query, $bindings);

    $contacts = [];
    $totalSum = 0;
    $monthlyCounts = [];
    // $months = collect($rawContacts)->pluck('month')->unique()->sort()->values()->all();
    $months = collect($rawContacts)
    ->pluck('month')
    ->unique()
    ->map(fn($m) => Carbon::createFromFormat('M-y', $m))
    ->sort()
    ->map(fn($m) => $m->format('M-y'))
    ->values()
    ->all();


    foreach ($rawContacts as $row) {
        $skala = $row->skala;
        $month = $row->month;
        $total = $row->total;

        if (!isset($contacts[$skala])) {
            $contacts[$skala] = [
                'skala' => $skala,
                'monthly_counts' => [],
                'total' => 0
            ];
        }

        $contacts[$skala]['monthly_counts'][$month] = $total;
        $contacts[$skala]['total'] += $total;
        $totalSum += $total;
        $monthlyCounts[$month] = ($monthlyCounts[$month] ?? 0) + $total;
    }

    foreach ($contacts as &$contact) {
        $contact['percentage'] = $totalSum > 0 ? ($contact['total'] / $totalSum) * 100 : 0;
    }

    $formattedData = collect($contacts)->values()->map(function ($data, $index) use ($months) {
        $row = [
            'No' => $index + 1,
            'skala' => $data['skala'],
        ];
        foreach ($months as $month) {
            $row[$month] = $data['monthly_counts'][$month] ?? 0;
        }
        $row['total'] = $data['total'];
        $row['percentage'] = number_format($data['percentage'], 2) . '%';
        return (object) $row;
    });

    return Excel::download(new SkalaExport($formattedData, $months), 'Skala_Report.xlsx');
}

// private function exportFilteredContacts(Request $request)
//     {
//         $user = Auth::user(); // Get the logged-in user
//         if ($user->lembaga_id == 1) {
//             $lembagas = DB::table('lembaga')->get();
//             $sql = "SELECT DISTINCT * FROM USERS WHERE role_id = 2";
//         } else {
//             $lembagas = DB::table('lembaga')->where('id', $user->lembaga_id)->get();
//             $sql = "SELECT DISTINCT * FROM USERS WHERE role_id = 2 and lembaga_id = $user->lembaga_id";
//         }
//         $names = DB::select($sql);

//         $lembaga = $request->filterLembaga;
//         $name = $request->filterName;
//         $startDate = $request->startDate;
//         $endDate = $request->endDate;

//         // Generate dynamic month columns
//         $months = [];
//         $start = Carbon::parse($startDate)->startOfMonth();
//         $end = Carbon::parse($endDate)->endOfMonth();
//         while ($start->lte($end)) {
//             $months[] = $start->format('M-y'); // Ex: Apr-24
//             $start->addMonth();
//         }

//         // Base query
//         $query = "SELECT contacts.utusan, ";
//         $bindings = [];

//         foreach ($months as $month) {
//             $safeMonthKey = str_replace('-', '_', $month);
//             $query .= "SUM(CASE WHEN to_char(contacts.created_at, 'Mon-YY') = :month_{$safeMonthKey} THEN 1 ELSE 0 END) AS \"{$month}\", ";
//             $bindings["month_{$safeMonthKey}"] = $month;
//         }

//         $query .= "COUNT(*) AS total FROM contacts 
//                JOIN users ON contacts.utusan_id = users.id WHERE 1=1 ";

//         if ($lembaga && $lembaga != '0') {
//             $query .= " AND users.lembaga_id = :lembaga";
//             $bindings['lembaga'] = $lembaga;
//         }
//         if ($name && $name != '0') {
//             $query .= " AND contacts.utusan_id = :name";
//             $bindings['name'] = $name;
//         }
//         if ($startDate) {
//             $query .= " AND contacts.created_at >= :startDate";
//             $bindings['startDate'] = Carbon::parse($startDate)->startOfMonth()->toDateString();
//         }
//         if ($endDate) {
//             $query .= " AND contacts.created_at <= :endDate";
//             $bindings['endDate'] = Carbon::parse($endDate)->endOfMonth()->toDateString();
//         }

//         $query .= " AND contacts.status = 1
//                         GROUP BY contacts.utusan";

//         // Execute Query
//         $contacts = DB::select($query, $bindings);

//         // Calculate Percentage
//         $totalSum = array_sum(array_column($contacts, 'total'));
//         foreach ($contacts as $contact) {
//             $contact->percentage = $totalSum > 0 ? ($contact->total / $totalSum) * 100 : 0;
//         }

//         return $contacts;
//     }

// private function exportFilteredContacts(Request $request)
// {
//     $user = Auth::user();
//     $lembagaId = $user->lembaga_id;
//     $roleId = $user->role_id;
//     $userRegionIds = json_decode($user->region_id, true);

//     $lembaga = $request->filterLembaga;
//     $name = $request->filterName;
//     $startDate = $request->startDate;
//     $endDate = $request->endDate;

//     // Generate dynamic month columns
//     $months = [];
//     $start = Carbon::parse($startDate)->startOfMonth();
//     $end = Carbon::parse($endDate)->endOfMonth();
//     while ($start->lte($end)) {
//         $months[] = $start->format('M-y');
//         $start->addMonth();
//     }

//     $query = "SELECT contacts.utusan, ";
//     $bindings = [];

//     foreach ($months as $month) {
//         $safeMonthKey = str_replace('-', '_', $month);
//         $query .= "SUM(CASE WHEN to_char(contacts.tanggal, 'Mon-YY') = :month_{$safeMonthKey} THEN 1 ELSE 0 END) AS \"{$month}\", ";
//         $bindings["month_{$safeMonthKey}"] = $month;
//     }

//     $query .= "COUNT(*) AS total FROM contacts 
//                JOIN users ON contacts.utusan_id = users.id 
//                WHERE 1=1 ";

//     // if ($roleId == 7 || $roleId == 5) {
//     //     $names = DB::select("SELECT id, username, name, role_id, lembaga_id FROM users WHERE role_id IN (1, 2, 6)");
//     //     $lembagas = DB::table('lembaga')->get();
//     // } elseif ($lembagaId != 1) {
//     //     $names = DB::select("SELECT id, username, name, role_id, lembaga_id FROM users WHERE role_id IN (1, 2, 6) AND lembaga_id = $lembagaId");
//     //     $lembagas = DB::table('lembaga')->where('id', $lembagaId)->get();
//     //     $query .= " AND contacts.lembaga_id = $lembagaId";
//     // } elseif ($roleId == 3) {
//     //     $regionList = implode(',', array_map(fn($id) => "'$id'", $userRegionIds));
//     //     $names = DB::select("
//     //         SELECT id, username, name, role_id, lembaga_id 
//     //         FROM users 
//     //         WHERE role_id IN (1, 2, 6) 
//     //           AND lembaga_id = $lembagaId 
//     //           AND EXISTS (
//     //             SELECT 1 FROM jsonb_array_elements_text(
//     //                 CASE 
//     //                     WHEN jsonb_typeof(users.region_id::jsonb) = 'array' 
//     //                     THEN users.region_id::jsonb 
//     //                     ELSE '[]'::jsonb 
//     //                 END
//     //             ) AS region 
//     //             WHERE region::text = ANY(ARRAY[$regionList])
//     //         )
//     //     ");
//     //     $lembagas = DB::table('lembaga')->where('id', $lembagaId)->get();
//     //     $query .= " AND contacts.region_id IN (" . implode(',', array_fill(0, count($userRegionIds), '?')) . ")";
//     //     $bindings = array_merge($bindings, $userRegionIds);
//     // } else {
//     //     $names = DB::select("SELECT id, username, name, role_id, lembaga_id FROM users WHERE id = $user->id");
//     //     $lembagas = DB::table('lembaga')->where('id', $lembagaId)->get();
//     //     $query .= " AND contacts.utusan_id = $user->id";
//     // }

//     if ($roleId == 7 || $roleId == 5) {
//         $names = DB::select("SELECT id, username, name, role_id, lembaga_id FROM users WHERE role_id IN (1, 2, 6)");
//         $lembagas = DB::table('lembaga')->get();
//     } elseif ($roleId == 3) {
//         $regionList = implode(',', array_map(fn($id) => "'$id'", $userRegionIds));
//         $names = DB::select("
//             SELECT id, username, name, role_id, lembaga_id 
//             FROM users 
//             WHERE role_id IN (1, 2, 6) 
//               AND lembaga_id = ?
//               AND EXISTS (
//                 SELECT 1 FROM jsonb_array_elements_text(
//                     CASE 
//                         WHEN jsonb_typeof(users.region_id::jsonb) = 'array' 
//                         THEN users.region_id::jsonb 
//                         ELSE '[]'::jsonb 
//                     END
//                 ) AS region 
//                 WHERE region::text = ANY(ARRAY[$regionList])
//             )
//         ", [$lembagaId]);
    
//         $lembagas = DB::table('lembaga')->where('id', $lembagaId)->get();
//         $query .= " AND contacts.region_id IN (" . implode(',', array_fill(0, count($userRegionIds), '?')) . ")";
//         $bindings = array_merge($bindings, $userRegionIds);
//     } else {
//         $names = DB::select("SELECT id, username, name, role_id, lembaga_id FROM users WHERE id = ?", [$user->id]);
//         $lembagas = DB::table('lembaga')->where('id', $lembagaId)->get();
//         $query .= " AND contacts.utusan_id = ?";
//         $bindings[] = $user->id;
//     }
    
//     // Apply lembaga filter if NOT role 7 or 5
//     if ($roleId != 7 && $roleId != 5 && $lembagaId != 1 && $roleId != 3) {
//         $query .= " AND contacts.lembaga_id = ?";
//         $bindings[] = $lembagaId;
    
//         $names = DB::select("
//             SELECT id, username, name, role_id, lembaga_id 
//             FROM users 
//             WHERE role_id IN (1, 2, 6) 
//               AND lembaga_id = ?
//         ", [$lembagaId]);
//     }

//     if ($lembaga && $lembaga != '0') {
//         $query .= " AND users.lembaga_id = :lembaga";
//         $bindings['lembaga'] = $lembaga;
//     }
//     if ($name && $name != '0') {
//         $query .= " AND contacts.utusan_id = :name";
//         $bindings['name'] = $name;
//     }
//     if ($startDate) {
//         $query .= " AND contacts.tanggal >= :startDate";
//         $bindings['startDate'] = Carbon::parse($startDate)->startOfMonth()->toDateString();
//     }
//     if ($endDate) {
//         $query .= " AND contacts.tanggal <= :endDate";
//         $bindings['endDate'] = Carbon::parse($endDate)->endOfMonth()->toDateString();
//     }

//     $query .= " AND contacts.status = 1 GROUP BY contacts.utusan";

//     $contacts = DB::select($query, $bindings);

//     $totalSum = array_sum(array_column($contacts, 'total'));
//     foreach ($contacts as $contact) {
//         $contact->percentage = $totalSum > 0 ? ($contact->total / $totalSum) * 100 : 0;
//     }

//     return $contacts;
// }

private function exportFilteredContacts(Request $request)
    {
        $user = Auth::user();
        $lembagaId = $user->lembaga_id;
        $roleId = $user->role_id;
        $userRegionIds = json_decode($user->region_id, true);

        $lembaga = $request->filterLembaga;
        $name = $request->filterName;
        $startDate = $request->startDate;
        $endDate = $request->endDate;

        $months = [];
        $start = Carbon::parse($startDate)->startOfMonth();
        $end = Carbon::parse($endDate)->endOfMonth();
        while ($start->lte($end)) {
            $months[] = $start->format('M-y');
            $start->addMonth();
        }

        $query = "SELECT users.name AS utusan, ";
        $bindings = [];

        foreach ($months as $month) {
            $safeMonthKey = str_replace('-', '_', $month);
            $query .= "SUM(CASE WHEN to_char(activity.tanggal, 'Mon-YY') = :month_{$safeMonthKey} THEN 1 ELSE 0 END) AS \"{$month}\", ";
            $bindings["month_{$safeMonthKey}"] = $month;
        }

        $query .= "COUNT(*) AS total
                    FROM (
                        SELECT DISTINCT ON (contact_id, to_char(tanggal, 'YYYY-MM')) contact_id, tanggal
                        FROM contacts_history
                    ) AS activity
                    JOIN contacts ON contacts.id = activity.contact_id
                    JOIN users ON contacts.utusan_id = users.id
                    WHERE 1=1 ";

        // --- START OF CORRECTED QUERY LOGIC ---

        if ($roleId == 3) {
            // Role 3 (Region-based access)
            $placeholders = [];
            foreach ($userRegionIds as $index => $regionId) {
                $placeholder = ":region{$index}";
                $placeholders[] = $placeholder;
                $bindings["region{$index}"] = $regionId;
            }
            if ($placeholders) {
                $query .= " AND contacts.region_id IN (" . implode(',', $placeholders) . ")";
            }
        } elseif (!in_array($roleId, [7, 5])) { // For roles NOT 7 or 5
            // If the user's lembaga_id is not 1, filter by their lembaga_id
            // Otherwise, if the user's lembaga_id is 1 (and they are not roles 7 or 5),
            // and they are not role 3 (handled above), then filter by their utusan_id.
            // This replicates the `contacts.utusan_id = $user->id` for non-admin/non-region users.
            if ($lembagaId != 1) {
                $query .= " AND contacts.lembaga_id = :lembagaUser";
                $bindings['lembagaUser'] = $lembagaId;
            } else {
                $query .= " AND contacts.utusan_id = :userId";
                $bindings['userId'] = $user->id;
            }
        }
        // If roleId is 7 or 5, no explicit filter related to contacts.utusan_id
        // or contacts.lembaga_id is applied here, mimicking the original behavior.

        // --- END OF CORRECTED QUERY LOGIC ---


        // Optional filters (these were already correct)
        if ($lembaga && $lembaga != '0') {
            $query .= " AND users.lembaga_id = :lembaga";
            $bindings['lembaga'] = $lembaga;
        }
        if ($name && $name != '0') {
            $query .= " AND contacts.utusan_id = :name";
            $bindings['name'] = $name;
        }
        if ($startDate) {
            $query .= " AND activity.tanggal >= :startDate";
            $bindings['startDate'] = Carbon::parse($startDate)->startOfMonth()->toDateString();
        }
        if ($endDate) {
            $query .= " AND activity.tanggal <= :endDate";
            $bindings['endDate'] = Carbon::parse($endDate)->endOfMonth()->toDateString();
        }

        $query .= " GROUP BY users.name";

        // Debugging line to see the generated SQL query (keep it for testing)
        // echo $query;
        // dd($bindings); // Use dd to inspect bindings during debugging

        $contacts = DB::select($query, $bindings);

        $totalSum = array_sum(array_map(fn($c) => $c->total, $contacts));
        foreach ($contacts as $contact) {
            $contact->percentage = $totalSum > 0 ? ($contact->total / $totalSum) * 100 : 0;
        }

        return $contacts;
    }

    public function exportToExcel(Request $request)
    {
        $contacts = $this->exportFilteredContacts($request);
        $months = $this->getMonths($request->startDate, $request->endDate);

        return Excel::download(new ContactsExport(collect($contacts), $months), 'contacts.xlsx');
    }

    private function getMonths($startDate, $endDate)
    {
        $start = Carbon::parse($startDate)->startOfMonth();
        $end = Carbon::parse($endDate)->endOfMonth();
        $months = [];

        while ($start->lte($end)) {
            $months[] = $start->format('M-y'); // Ex: Apr-24
            $start->addMonth();
        }

        return $months;
    }

    public function totalSDM(Request $request)
    {
        $lembagaId = $request->query('lembaga_id');

        $query = DB::table('sdm')
            ->select('lembaga_id', 'kategori_sdm', DB::raw('COUNT(*) as total'))
            ->groupBy('lembaga_id', 'kategori_sdm');

        if ($lembagaId) {
            $query->where('lembaga_id', $lembagaId);
        }

        $counts = $query->get();

        $lembagas = DB::table('lembaga')->select('id', 'name')->get();

        $beasiswaCount = DB::table('sdm')
            ->where('pendidikan->beasiswa_parousia->status', 'Ya')
            ->when($lembagaId, fn ($q) => $q->where('lembaga_id', $lembagaId))
            ->count();

        return view('report.sdm-total', [
            'counts' => $counts,
            'lembagas' => $lembagas,
            'selectedLembaga' => $lembagaId,
            'beasiswaCount' => $beasiswaCount,
        ]);
    }

    public function exportJumlahSDM(Request $request){
        $lembagaId = $request->query('lembaga_id');

        return Excel::download(new SdmReportExport($lembagaId), 'sdm-report.xlsx');
    }

    public function userTrainingReport(Request $request)
    {
        $nameFilter = $request->query('name');

         // Step 1: Get total training pages across all branches
        $totalGlobalPages = DB::table('training_pages')
                            ->where('branch_id', '!=', null)
                            ->count();

        // Step 2: Fetch user training data
        $trainings = DB::table('trainings as t')
            ->leftJoin('training_branches as b', 'b.id', '=', 't.branch_id')
            ->leftJoin('users as u', 'u.id', '=', 't.user_id')
            ->select(
                't.user_id',
                'u.name as user_name',
                'b.name as branch_name',
                't.current_page',
                't.completed_pages'
            )
            ->when($nameFilter, function ($query) use ($nameFilter) {
                $query->where('u.name', 'ilike', '%' . $nameFilter . '%');
            })
            ->get();

        // Step 3: Map to report format
        $report = $trainings->map(function ($item) use ($totalGlobalPages) {
            $completed = json_decode($item->completed_pages ?? '[]', true);
            $completedCount = count($completed);

            $progress = $totalGlobalPages > 0
                ? round(($completedCount / $totalGlobalPages) * 100, 2)
                : 0;

            return [
                'user_name' => $item->user_name ?? 'Unknown',
                'branch' => $item->branch_name ?? '-',
                'current_page' => $item->current_page,
                'total_pages' => $totalGlobalPages,
                'completed_pages' => $completedCount,
                'progress_percent' => $progress . '%',
            ];
        });

        return view('report.user-training', compact('report'));
    }

    public function exportTraining(Request $request)
    {
        $name = $request->query('name');

    return Excel::download(
        new UserTrainingReportExport($name),
        'user-training-report.xlsx'
    );
    }
}





