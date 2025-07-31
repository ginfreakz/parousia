<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Carbon;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = auth()->user();
        $jml_contact = DB::table('contacts')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->where('utusan_id', $user->id)
            ->count();

        $today = Carbon::now()->format('m-d');
        $birthdayContacts = DB::table('sdm')
            ->whereRaw("TO_CHAR(tanggal_lahir, 'MM-DD') = ?", [$today])
            ->get();

        $target = DB::table('target_settings')->first();

        return view('admin.dashboard', compact('user', 'jml_contact', 'birthdayContacts', 'target'));
    }

    public function indexTarget()
    {
        $target = DB::table('target_settings')->first(); 
        return view('admin.manage-target', compact('target'));
    }


    public function update(Request $request)
    {
        $request->validate([
            'target_max' => 'required|integer|min:1',
            'target_min' => 'required|integer|min:1',
            'target_normal' => 'required|integer|min:1',
        ]);

        DB::table('target_settings')->updateOrInsert(
            ['id' => 1], // The condition to check if the record exists (e.g., based on `id`)
            [
                'target_max' => $request->target_max,
                'target_min' => $request->target_min,
                'target_normal' => $request->target_normal,
                'updated_at' => now(),
            ]
        );

        return redirect()->back()->with('success', 'Target settings updated successfully!');
    }
}