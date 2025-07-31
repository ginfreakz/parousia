<?php

namespace App\Exports;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ContactListExport implements FromCollection, WithHeadings
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $user = Auth::user();
        $lembagaId = $user->lembaga_id;
        $roleId = $user->role_id;
        $userRegionIds = json_decode($user->region_id, true);

        $sql = "SELECT contacts.id, contacts.name, contacts.skala, contacts.utusan, 
                       contacts.tanggal, contacts.description, contacts.status, 
                       regions.kota, regions.provinsi 
                FROM contacts 
                JOIN regions ON contacts.region_id = regions.id
                WHERE 1=1";

        if ($roleId == 7 || $roleId == 5) {
            // All access
        } elseif ($roleId == 3) {
            // $userRegions = DB::table('regions')->whereIn('id', $userRegionIds)->pluck('provinsi')->unique();
            // if ($userRegions->isNotEmpty()) {
            //     $provinsiList = "'" . implode("','", $userRegions->toArray()) . "'";
            //     $sql .= " AND regions.provinsi IN ($provinsiList)";
            // }

            $userKotas = DB::table('regions')->whereIn('id', $userRegionIds)->pluck('kota')->unique();
            if ($userKotas->isNotEmpty()) {
                $kotaList = "'" . implode("','", $userKotas->toArray()) . "'";
                $sql .= " AND regions.kota IN ($kotaList)";
            }
        } else {
            $userKotas = DB::table('regions')->whereIn('id', $userRegionIds)->pluck('kota')->unique();
            $sql .= " AND contacts.utusan_id = " . $user->id;
            if ($userKotas->isNotEmpty()) {
                $kotaList = "'" . implode("','", $userKotas->toArray()) . "'";
                $sql .= " AND regions.kota IN ($kotaList)";
            }
        }

        if ($lembagaId != 1) {
            $sql .= " AND contacts.lembaga_id = " . $lembagaId;
        }

        $r = $this->request;
        if ($r->filled('name')) {
            $sql .= " AND contacts.name ILIKE '%" . $r->name . "%'";
        }
        if ($r->filled('skala')) {
            $sql .= " AND contacts.skala = '" . $r->skala . "'";
        }
        if ($r->filled('utusan')) {
            $sql .= " AND contacts.utusan ILIKE '%" . $r->utusan . "%'";
        }
        if ($r->filled('region')) {
            $sql .= " AND (regions.kota ILIKE '%" . $r->region . "%' OR regions.provinsi LIKE '%" . $r->region . "%')";
        }
        if ($r->filled('startDate') && $r->filled('endDate')) {
            $sql .= " AND contacts.tanggal BETWEEN '" . $r->startDate . "' AND '" . $r->endDate . "'";
        } elseif ($r->filled('startDate')) {
            $sql .= " AND contacts.tanggal >= '" . $r->startDate . "'";
        } elseif ($r->filled('endDate')) {
            $sql .= " AND contacts.tanggal <= '" . $r->endDate . "'";
        }

        $rawResults = DB::select($sql);

        // Add sequence number instead of contact ID
        return collect($rawResults)->map(function ($row, $index) {
            return [
                'no' => $index + 1,
                'name' => $row->name,
                'skala' => $row->skala,
                'utusan' => $row->utusan,
                'tanggal' => $row->tanggal,
                'description' => $row->description,
                'status' => $row->status,
                'kota' => $row->kota,
                'provinsi' => $row->provinsi,
            ];
        });
    }

    public function headings(): array
    {
        return ['No', 'Name', 'Skala', 'Utusan', 'Tanggal', 'Description', 'Status', 'Kota', 'Provinsi'];
    }

}
