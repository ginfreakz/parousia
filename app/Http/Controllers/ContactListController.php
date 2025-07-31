<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;
use App\Models\Region;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ContactListExport;
use App\Models\User; // Ensure User model is imported





class ContactListController extends Controller
{
    public function index(Request $request)
{
    $user = Auth::user();
    $lembagaId = $user->lembaga_id;
    $roleId = $user->role_id;
    $userRegionIds = json_decode($user->region_id, true);

    $sql = "SELECT contacts.id, contacts.name, contacts.skala, contacts.utusan, 
                contacts.tanggal, contacts.description, contacts.status, regions.kota, regions.provinsi 
         FROM contacts 
         JOIN regions ON contacts.region_id = regions.id
         WHERE 1=1";

    // Apply filters based on user role and organization
    // if ($roleId == 7 || $roleId == 5) {
    //     // User can see all data
    //     $regionSql = "SELECT * FROM regions";
    //     $regions = DB::select($regionSql);
    // } elseif ($lembagaId != 1) {
    //     // User with lembaga_id = 2 can only see contacts with the same lembaga_id
    //     $sql .= " AND contacts.lembaga_id = " . $lembagaId;
    //     $regionSql = "SELECT * FROM regions WHERE id IN (" . implode(',', $userRegionIds) . ")";
    //     $regions = DB::select($regionSql);
    // } elseif ($roleId == 3) {
    //     // User with role_id = 3 can see contacts in the same region.provinsi
    //     $userRegions = DB::table('regions')->whereIn('id', $userRegionIds)->pluck('provinsi')->unique();
    //     $regionSql = "SELECT * FROM regions WHERE id IN (" . implode(',', $userRegionIds) . ")";
    //     $regions = DB::select($regionSql);
    //     if ($userRegions->isNotEmpty()) {
    //         $provinsiList = "'" . implode("','", $userRegions->toArray()) . "'";
    //         $sql .= " AND regions.provinsi IN ($provinsiList)";
    //         }
    // } else{
    //     // User with role_id in [1, 2] can only see contacts created by themselves
    //     $userKotas = DB::table('regions')->whereIn('id', $userRegionIds)->pluck('kota')->unique();
    //     $regionSql = "SELECT * FROM regions WHERE id IN (" . implode(',', $userRegionIds) . ")";
    //     //$regionSql = "SELECT * FROM regions";
    //     $regions = DB::select($regionSql);
    //     $sql .= " AND contacts.utusan_id = " . $user->id;

    //     if ($userKotas->isNotEmpty()) {
    //         $kotaList = "'" . implode("','", $userKotas->toArray()) . "'";
    //         $sql .= " AND regions.kota IN ($kotaList)";
    //     }
    //     //dd($user->region_id, json_decode($user->region_id, true));
    // }

    if ($roleId == 7 || $roleId == 5) {
        $regionSql = "SELECT * FROM regions";
        $regions = DB::select($regionSql);
    } elseif ($roleId == 3) {
        // $userRegions = DB::table('regions')->whereIn('id', $userRegionIds)->pluck('provinsi')->unique();
        $regionSql = "SELECT * FROM regions WHERE id IN (" . implode(',', $userRegionIds) . ")";
        $regions = DB::select($regionSql);
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
        $regionSql = "SELECT * FROM regions WHERE id IN (" . implode(',', $userRegionIds) . ")";
        $regions = DB::select($regionSql);
        $sql .= " AND contacts.utusan_id = " . $user->id;
        if ($userKotas->isNotEmpty()) {
            $kotaList = "'" . implode("','", $userKotas->toArray()) . "'";
            $sql .= " AND regions.kota IN ($kotaList)";
        }
    }
    
    // Apply lembaga filter after base logic
    if ($lembagaId != 1) {
        $sql .= " AND contacts.lembaga_id = " . $lembagaId;
    }
    
    //echo $sql;


if ($request->filled('name')) {
    $sql .= " AND contacts.name ILIKE '%" . $request->name . "%'";
}

if ($request->filled('skala')) {
    $sql .= " AND contacts.skala = '" . $request->skala . "'";
}

if ($request->filled('utusan')) {
    $sql .= " AND contacts.utusan ILIKE '%" . $request->utusan . "%'";
}

if ($request->filled('region')) {
    $sql .= " AND (regions.kota ILIKE '%" . $request->region . "%')";
}

if ($request->filled('startDate') && $request->filled('endDate')) {
    $sql .= " AND contacts.tanggal BETWEEN '" . $request->startDate . "' AND '" . $request->endDate . "'";
} elseif ($request->filled('startDate')) {
    $sql .= " AND contacts.tanggal >= '" . $request->startDate . "'";
} elseif ($request->filled('endDate')) {
    $sql .= " AND contacts.tanggal <= '" . $request->endDate . "'";
}

$contacts = DB::select($sql);


    // Return JSON for AJAX
    if ($request->ajax()) {
        return response()->json(['contacts' => $contacts]);
    }

    $utusanSql = "SELECT DISTINCT utusan_id, utusan FROM contacts";
    $listUtusan = DB::select($utusanSql);


    return view('misionaris.contact-list', compact('contacts', 'regions', 'listUtusan'))->with('userRole', $user->role_id);
}
    

    public function store(Request $request)
    {
        $user = Auth::user();
        $roleId = $user->role_id;

        $regionParts = explode('-', $request->region_id);

        if($roleId == 7){
            $utusanParts = explode('-', $request->utusan);
            $utusanId = $utusanParts[0];
            $utusanName = $utusanParts[1];

            $lembagaIdSql = "SELECT lembaga_id FROM users WHERE id = " . $utusanId;
            $lembagaId = DB::select($lembagaIdSql);
        }

        if (count($regionParts) !== 2) {
            return back()->withErrors(['region_id' => 'Invalid region selection.']);
        }
        
        $regionId = $regionParts[0];
        $regionName = $regionParts[1];
        
        
        $oneWeekAgo = Carbon::now()->subWeek()->toDateString();

        // $request->validate([
        //     'name' => 'required|string|max:255|unique:contacts,name',
        //     // 'skala' => 'required|integer',
        //     'region_id' => 'required|string', // Ensure region_id is passed
        //     'tanggal' => 'required|date',
        //     // 'description' => 'required|string|max:80',
        // ], [
        //     // 'tanggal.after_or_equal' => 'The date cannot be more than 1 week old.',
        //     'picture_path' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        //     // 'description.max' => 'The description must not exceed 80 characters.', 
            
        // ]);

        $request->validate([
            'name' => 'required|string|max:255|unique:contacts,name',
            // 'skala' => 'required|integer',
            'region_id' => 'required|string', // Ensure region_id is passed
            'tanggal' => 'required|date',
            // 'description' => 'required|string|max:80',
        ], [
            // 'tanggal.after_or_equal' => 'The date cannot be more than 1 week old.',
            'picture_path' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            // 'description.max' => 'The description must not exceed 80 characters.',
            'name.unique' => 'The name has already been taken.', 
            
        ]);

        $picturePath = null;
        if ($request->hasFile('picture')) {
            $picture = $request->file('picture');
            $picturePath = $picture->store('pictures', 'public'); // Store in the 'storage/app/public/pictures' folder
        }

        $contact = Contact::create([
            'name' => $request->name,
            'status' => '1',
            'skala' => '-3',
            'utusan' => $utusanName ?? auth()->user()->name,
            'utusan_id' => $utusanId ?? auth()->user()->id,
            'phone' => $request->phone,
            'region_id' => $regionId, // Parsed region ID
            'region' => $regionName,  // Parsed region name
            'tanggal' => $request->tanggal,
            'description' => $request->description,
            'kelamin' => $request->kelamin,
            'usia' => $request->usia,
            'ulang_tahun' => $request->ulang_tahun,
            'pekerjaan' => $request->pekerjaan,
            'suku' => $request->suku,
            'picture_path' => $picturePath,
            'lembaga_id' => $lembagaId[0]->lembaga_id ?? auth()->user()->lembaga_id,
        ]);

        DB::table('contacts_history')->insert([
            'contact_id' => $contact->id,
            'name' => $request->name,
            'skala' => '-3',
            'utusan' => $utusanName ?? auth()->user()->name,
            'tanggal' => $request->tanggal,
            'description' => $request->description,
            'created_at' => now(),
            'updated_at' => now(),
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('contactlist.index')->with('status', 'Contact created successfully!');
                 
    }

    public function edit($id)
    {
        $user = Auth::user();
        $roleId = $user->role_id;
        $lembagaId = $user->lembaga_id;
        $userRegionIds = (array) json_decode($user->region_id, true);

        $contact = Contact::findOrFail($id);

        if($roleId == 7){
            $utusanSql = "SELECT DISTINCT(utusan) from contacts";
            $listUtusan = DB::select($utusanSql);
            $regionSql = "SELECT * FROM regions";
            $regions = DB::select($regionSql);
        } else{
            $utusanSql = "SELECT DISTINCT(utusan) from contacts WHERE utusan   = '" . $user->name . "'";
            $listUtusan = DB::select($utusanSql);
            $regionSql = "SELECT * FROM regions WHERE id IN (" . implode(',', $userRegionIds) . ")";
            $regions = DB::select($regionSql);
        }
        
        
        return view('misionaris.edit-contact', compact('contact', 'regions', 'listUtusan'));
    }

    public function update(Request $request, $id)
    {
        $regionParts = explode('-', $request->region_id);

        if (count($regionParts) !== 2) {
            return back()->withErrors(['region_id' => 'Invalid region selection.']);
        }
        
        $regionId = $regionParts[0];
        $regionName = $regionParts[1];

        $request->validate([
            'name' => 'required|string|max:255',
            'utusan' => 'required|string|max:255',
            'region_id' => 'required|string',
        ]);

        $contact = Contact::findOrFail($id);
        $contact->update([ 
            'status' => $request->status,
            'name' => $request->name,
            'utusan' => $request->utusan,
            'phone' => $request->phone,
            'region_id' => $regionId,
            'description' => $request->description,
            'region' => $regionName,
            'kelamin' => $request->kelamin,
            'usia' => $request->usia,
            'pekerjaan' => $request->pekerjaan,
            'suku' => $request->suku,
        ]);

        return redirect()->route('contactlist.index')->with('status', 'Contact updated successfully!')
                 ->header('Content-Type', 'text/html');

    }

    public function destroy($id)
    {
        $contact = Contact::findOrFail($id);
        $contact->delete();

        return redirect()->route('contactlist.index')->with('status', 'Contact deleted successfully!');
    }
    
    public function history($id)
{
    try {
        // Fetch the history records for the contact
        $history = DB::table('contacts_history')
            ->where('contact_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        $latestSkala = DB::table('contacts_history')
            ->where('contact_id', $id)
            ->orderBy('created_at', 'desc')
            ->value('skala');

        // Optionally, fetch user names for updated_by
        $history = $history->map(function ($entry) {
            $entry->updated_by_name = $entry->updated_by ? User::find($entry->updated_by)->name : null;
            return $entry;
        });

        // Debugging output
        return response()->json([
            'history' => $history,
            'latest_skala' => $latestSkala,
        ], 200);
    } catch (\Exception $e) {

        return response()->json([
            'error' => 'Failed to fetch history data',
            'message' => $e->getMessage(),
        ], 500);
    }
    }

    public function deleteHistory($id)
{
    try {
        // Find the history entry by ID and delete it
        $history = DB::table('contacts_history')->where('id', $id)->first();

        if (!$history) {
            return response()->json([
                'error' => 'History record not found',
            ], 404);
        }

        // Delete the history record
        DB::table('contacts_history')->where('id', $id)->delete();

        return response()->json([
            'success' => 'History record deleted successfully.',
        ], 200);
    } catch (\Exception $e) {

        return response()->json([
            'error' => 'Failed to delete history record',
            'message' => $e->getMessage(),
        ], 500);
    }
}

    public function updateHistory(Request $request)
{
    $request->validate([
        'contact_id' => 'required|exists:contacts,id',
        'tanggal' => 'required|date',
        'skala' => 'required|integer|min:-3|max:3',
        'description' => 'required|string|max:255',
    ]);

    try {
        // Fetch the contact details based on contact_id
        $contact = DB::table('contacts')->where('id', $request->contact_id)->first();

        if (!$contact) {
            return response()->json(['error' => 'Contact not found.'], 404);
        }

        // Insert the new history record
        DB::table('contacts_history')->insert([
            'contact_id' => $request->contact_id,
            'name' => $contact->name,
            'skala' => $request->skala,
            'utusan' => auth()->user()->name,
            'tanggal' => $request->tanggal,
            'description' => $request->description,
            'created_at' => now(),
            'updated_at' => now(),
            'updated_by' => Auth::id(),
        ]);
        DB::table('contacts')->where('id', $request->contact_id)->update([
            'skala' => $request->skala,
            'updated_at' => now(),
            'tanggal' => $request->tanggal,
            'description' => $request->description,
        ]);

        return response()->json(['message' => 'History updated successfully!'], 200);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Failed to update history.', 'message' => $e->getMessage()], 500);
    }
}
    public function show($id)
{
    try {
        $contact = Contact::select([
            'id', 'name', 'skala', 'kelamin', 'suku', 'usia', 
            'ulang_tahun', 'phone', 'pekerjaan', 'region', 'tanggal', 
            'description', 'picture_path'
        ])->findOrFail($id);

        return response()->json($contact);
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        return response()->json(['error' => 'Contact not found'], 404);
    }
}

    public function uploadPicture(Request $request, $contactId)
    {
        // Validate the uploaded file
        $request->validate([
            'picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // max 2MB
        ]);

        // Retrieve the file from the request
        $file = $request->file('picture');

        // Define the target directory
        $destinationPath = public_path('picture_path'); // public/picture_path

        $contactId = $request->input('contact_id');
        $contact = Contact::findOrFail($contactId);

        // Create the directory if it doesn't exist
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }
        
        $fileName = $contact->name.'_'.$file->getClientOriginalName();

        // Move the file to the target directory
        $file->move($destinationPath, $fileName);

        // Update the database with the new picture path
        $sql = "UPDATE contacts SET picture_path = ? WHERE id = ?";
        DB::update($sql, [$fileName, $contactId]);
        
        // Return a success response
        return response()->json([
            'message' => 'Picture uploaded successfully!',
            'file_name' => $fileName,
            'file_path' => asset('picture_path/' . $fileName),
        ], 200);
    }

    public function deletePicture($id)
    {
        try {
            // Find the contact by ID
            $contact = Contact::findOrFail($id);

            // Set the picture_path column to null
            $contact->picture_path = null;
            $contact->save();

            return response()->json(['message' => 'Picture deleted successfully!'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete picture.'], 500);
        }
    }

    public function exportContactExcel(Request $request)
{
    return Excel::download(new ContactListExport($request), 'contact-list.xlsx');
}
}
