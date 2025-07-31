<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Region;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public function index()
    {
        $users = User::with(['role', 'region'])->get();
        $roles = Role::all();
        $regions = Region::all();
        return view('admin.manage-user', compact('users', 'roles', 'regions'));
    }

   public function updateUserRole(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'nullable|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Passwords do not match.');
        }

        $user = User::findOrFail($id);
        $user->role_id = $request->role_id;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('manage-user.index')->with('success', 'Updated successfully!');
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('manage-user.index')->with('status', 'User deleted successfully!');
    }

public function indexGroup()
{
    $user = Auth::user();
    $userRegionIds = json_decode($user->region_id, true) ?? []; // Ensure it's an array

    $sql = "SELECT * FROM groups WHERE 1=1";
    // if($user->role_id == 5 || $user->role_id == 7){
    //     $leaders = DB::select("SELECT * FROM users WHERE role_id IN ( 1, 2, 6 )");
    //     $contacts = DB::table('contacts')->get();
    // } elseif ($user->lembaga_id != 1) {
    //     // Users can only see groups with the same lembaga_id
    //     $sql .= " AND leader_id IN (SELECT id FROM users WHERE lembaga_id = $user->lembaga_id)";
    //     $leaders = DB::select("SELECT * FROM users WHERE role_id IN ( 1, 2, 6 ) AND lembaga_id = $user->lembaga_id");
    //     $contacts = DB::table('contacts')->where('lembaga_id', $user->lembaga_id)->get();
    // } elseif ($user->role_id == 3) {
    //     // Users with role_id 3 can only see groups with the same region_id
    //     $userRegionIds = array_map('intval', $userRegionIds); // Ensure values are integers
    //     $sql .= " AND leader_id IN (
    //         SELECT id FROM users 
    //         WHERE region_id::jsonb @> '" . json_encode($userRegionIds) . "'
    //     )";
    //     $leaders = DB::select("SELECT * FROM users WHERE role_id IN ( 1, 2, 6 ) AND region_id::jsonb @> '" . json_encode($userRegionIds) . "'");
    //     $contacts = DB::table('contacts')
    //                 ->whereIn('region_id', $userRegionIds)
    //                 ->get();
    // } else {
    //     $sql .= " AND leader_id = $user->id"; // Users with role_id 1, 2, 6 see their own groups
    //     $leaders = DB::select("SELECT * FROM users WHERE id = $user->id");
    //     $contacts = DB::table('contacts')
    //                 ->whereIn('region_id', $userRegionIds)
    //                 ->get();
    // }

    if ($user->role_id == 5 || $user->role_id == 7) {
        $leaders = DB::select("SELECT * FROM users WHERE role_id IN (1, 2, 6)");
        $contacts = DB::table('contacts')->get();

    } elseif ($user->lembaga_id != 1 && in_array($user->role_id, [1, 2, 6])) {
        // Restrict: only user's own groups, self as leader, self as contact utusan
        $sql .= " AND leader_id = {$user->id}";
        $leaders = DB::select("SELECT * FROM users WHERE id = {$user->id}");
        $contacts = DB::table('contacts')->where('utusan_id', $user->id)->get();

    } elseif ($user->lembaga_id != 1) {
        $sql .= " AND leader_id IN (
            SELECT id FROM users WHERE lembaga_id = {$user->lembaga_id}
        )";
        $leaders = DB::select("SELECT * FROM users WHERE role_id IN (1, 2, 6) AND lembaga_id = {$user->lembaga_id}");
        $contacts = DB::table('contacts')->where('lembaga_id', $user->lembaga_id)->get();

    } elseif ($user->role_id == 3) {
        $jsonRegion = json_encode($userRegionIds);
        $sql .= " AND leader_id IN (
            SELECT id FROM users WHERE region_id::jsonb @> '$jsonRegion'
        )";
        $leaders = DB::select("SELECT * FROM users WHERE role_id IN (1, 2, 6) AND region_id::jsonb @> '$jsonRegion'");
        $contacts = DB::table('contacts')->whereIn('region_id', $userRegionIds)->get();

    } else {
        $sql .= " AND leader_id = {$user->id}";
        $leaders = DB::select("SELECT * FROM users WHERE id = {$user->id}");
        $contacts = DB::table('contacts')->where('utusan_id', $user->id)->get();
    }

    $groups = DB::select($sql);

    // Fetch member names for each group
    foreach ($groups as $group) {
        $memberIds = json_decode($group->members, true) ?? []; // Ensure members is an array
        $group->member_names = DB::table('contacts')
            ->whereIn('id', $memberIds)
            ->pluck('name')
            ->toArray();
    }

    $userRole = $user->role_id;


    return view('admin.manage-group', ['groups' => $groups, 'leaders' => $leaders, 'contacts' => $contacts, 'userRole' => $userRole]);
}


    private function generateNextGroupName()
    {
        $lastGroup = DB::table('groups')->orderBy('id', 'desc')->first();
        if (!$lastGroup) {
            return 'A-01';
        }

        $lastGroupName = $lastGroup->name;
        $prefix = substr($lastGroupName, 0, 1);
        $number = (int)substr($lastGroupName, 2);

        if ($number < 99) {
            $number++;
        } else {
            $prefix++;
            $number = 1;
        }

        return sprintf('%s-%02d', $prefix, $number);
    }

    public function storeGroup(Request $request)
{
    $validatedData = $request->validate([
        'name' => 'required|unique:groups',
        'leader_id' => 'required',
        'members' => 'required|array',
    ]);

    $leaderParts = explode(' - ', $validatedData['leader_id']);

    if (count($leaderParts) < 2) {
        return redirect()->back()->withErrors(['leader_id' => 'Invalid leader format.']);
    }

    $leader_id = $leaderParts[0];
    $leader = $leaderParts[1];

    DB::table('groups')->insert([
        'name' => $request->name,
        'leader' => $leader,
        'leader_id' => $leader_id,
        'members' => json_encode($validatedData['members']),
        'created_at' => now(),
        'created_by' => Auth::id(),
        'updated_at' => now(),
        'updated_by' => Auth::id()
    ]);

    return redirect()->route('manage-group.index')->with('status', 'Group created successfully!');
}


    public function deleteGroup($id)
    {
        DB::table('groups')->where('id', $id)->delete();

        return redirect()->route('manage-group.index')->with('status', 'Group deleted successfully!');
    }

    public function updateGroup(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'leader_id' => 'required',
            'members' => 'required|array',
        ]);
    
        $leaderParts = explode(' - ', $validatedData['leader_id']);
        if (count($leaderParts) < 2) {
            return redirect()->back()->withErrors(['leader_id' => 'Invalid leader format.']);
        }
        $leader_id = $leaderParts[0];
        $leader = $leaderParts[1];
    
        DB::table('groups')->where('id', $id)->update([
            'name' => $validatedData['name'],
            'leader' => $leader,
            'leader_id' => $leader_id,
            'members' => json_encode($validatedData['members']),
            'updated_at' => now(),
            'updated_by' => Auth::id()
        ]);

        return redirect()->route('manage-group.index')->with('status', 'Group updated successfully!');
    }

    public function getGroupMembers($groupId)
    {
        $group = DB::table('groups')->where('id', $groupId)->first();
        $memberIds = json_decode($group->members, true);
        $members = DB::table('contacts')->whereIn('id', $memberIds)->get();

        return response()->json(['members' => $members]);
    }

// public function massUpdateHistory(Request $request)
//     {
//         $request->validate([
//             'group_id' => 'required|exists:groups,id',
//             'members' => 'required|array',
//             'members.*.id' => 'required|exists:contacts,id',
//             'members.*.tanggal' => 'required|date',
//             'members.*.skala' => 'required|integer|min:-3|max:3',
//             'members.*.description' => 'required|string|max:255',
//         ]);

//         try {
//             $group = DB::table('groups')->where('id', $request->group_id)->first();

//             if (!$group) {
//                 return redirect()->route('manage-group.index')->with('error', 'Group not found.');
//             }

//             foreach ($request->members as $member) {
//                 DB::table('contacts_history')->insert([
//                     'contact_id' => $member['id'],
//                     'name' => DB::table('contacts')->where('id', $member['id'])->value('name'),
//                     'skala' => $member['skala'],
//                     'utusan' => auth()->user()->name,
//                     'tanggal' => $member['tanggal'],
//                     'description' => $member['description'],
//                     'created_at' => now(),
//                     'updated_at' => now(),
//                     'updated_by' => Auth::id(),
//                 ]);

//                 // Update the main contact's skala
//                 DB::table('contacts')->where('id', $member['id'])->update([
//                     'skala' => $member['skala'],
//                     'updated_at' => now(),
//                 ]);
//             }

//             return redirect()->route('manage-group.index')->with('status', 'Mass update successful!');
//         } catch (\Exception $e) {
//             return redirect()->route('manage-group.index')->with('error', 'Failed to perform mass update: ' . $e->getMessage());
//         }
//     }

public function massUpdateHistory(Request $request)
{
    $request->validate([
        'group_id' => 'required|exists:groups,id',
        'members' => 'nullable|array',
    ]);

    $submittedMembers = collect($request->members)->filter(function ($member) {
        return !empty($member['tanggal']) && !empty($member['description']);
    });

    if ($submittedMembers->isEmpty()) {
        return redirect()->back()->with('error', 'Please fill both Tanggal and Description for at least one member.');
    }

    foreach ($submittedMembers as $member) {
        Validator::make($member, [
            'id' => 'required|exists:contacts,id',
            'tanggal' => 'required|date',
            'skala' => 'required|integer|min:-3|max:3',
            'description' => 'required|string|max:255',
        ])->validate();
    }

    try {
        foreach ($submittedMembers as $member) {
            DB::table('contacts_history')->insert([
                'contact_id' => $member['id'],
                'name' => DB::table('contacts')->where('id', $member['id'])->value('name'),
                'skala' => $member['skala'],
                'utusan' => auth()->user()->name,
                'tanggal' => $member['tanggal'],
                'description' => $member['description'],
                'created_at' => now(),
                'updated_at' => now(),
                'updated_by' => Auth::id(),
            ]);

            DB::table('contacts')->where('id', $member['id'])->update([
                'skala' => $member['skala'],
                'updated_at' => now(),
            ]);
        }

        return redirect()->route('manage-group.index')->with('status', 'Mass update successful!');
    } catch (\Exception $e) {
        return redirect()->route('manage-group.index')->with('error', 'Failed to perform mass update: ' . $e->getMessage());
    }
}




public function massUpdateGroup()
    {
        $user = Auth::user();
        if($user->role_id != 7){
            $groups = DB::table('groups')
                ->where('leader_id', $user->id)
                ->get();
        }else{
            $groups = DB::table('groups')->get();
        }
        return view('admin.mass-update-group', compact('groups'));
    }

    public function getMembers($groupId)
    {
        $user = Auth::user();
        $userRole = $user->role_id;
        // Retrieve JSON string of member IDs from the 'groups' table
        $membersJson = DB::table('groups')
            ->where('id', $groupId)
            ->value('members');

        // Decode JSON into an array (handle NULL case)
        $memberIds = $membersJson ? json_decode($membersJson, true) : [];

        if (empty($memberIds)) {
            return response()->json(['members' => []]); // No members found
        }

        // Fetch member details from 'contacts' where id is in the decoded array
        $members = DB::table('contacts')
        ->whereIn('contacts.id', $memberIds)
        ->leftJoin('contacts_history as ch', function ($join) {
            $join->on('contacts.id', '=', 'ch.contact_id')
                 ->whereRaw('ch.created_at = (SELECT MAX(created_at) FROM contacts_history WHERE contact_id = contacts.id)');
        })
        ->select('contacts.id', 'contacts.name', DB::raw('COALESCE(ch.skala, -3) as latest_skala'))
        ->get();

        return response()->json(['members' => $members, 'userRole' => $userRole]);
    }


}