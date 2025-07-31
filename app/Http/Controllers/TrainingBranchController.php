<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrainingBranchController extends Controller
{
    public function index()
    {
        $branches = DB::table('training_branches')->orderBy('order')->get();
        return view('training.chapters.index', compact('branches'));
    }

    public function create()
    {
        return view('training.chapters.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'order' => 'required|integer|min:0',
        ]);

        $branchId = DB::table('training_branches')->insertGetId([
            'name' => $request->name,
            'order' => $request->order,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('training_pages')->insert([
            'title' => 'Page 1',
            'content' => 'Welcome to your first training page!',
            'order' => 1,
            'branch_id' => $branchId,
            'page_number' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('training.chapters.index')->with('status', 'Chapter added successfully.');
    }

    public function edit($id)
    {
        $branch = DB::table('training_branches')->where('id', $id)->first();
        abort_if(!$branch, 404);
        return view('training.chapters.edit', compact('branch'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'order' => 'required|integer|min:0',
        ]);

        DB::table('training_branches')->where('id', $id)->update([
            'name' => $request->name,
            'order' => $request->order,
            'updated_at' => now(),
        ]);

        return redirect()->route('training.chapters.index')->with('status', 'Chapter updated successfully.');
    }

    public function destroy($id)
    {
        DB::table('training_branches')->where('id', $id)->delete();
        return redirect()->route('training.chapters.index')->with('status', 'Chapter deleted.');
    }
}
