<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\TrainingPage;

class TrainingController extends Controller
{
    public function show($page)
    {
        $user = Auth::user();
        if (!$user) return redirect()->route('login');

        // Get current branch from session
        $branchId = session('active_branch_id');
        if (!$branchId) {
            return redirect()->route('training.chapters')->with('error', 'No training chapter selected.');
        }

        // Get user progress in this branch
        $trainingProgress = DB::table('trainings')
            ->where('user_id', $user->id)
            ->where('branch_id', $branchId)
            ->first();

        // Create new if not exists
        if (!$trainingProgress) {
            DB::table('trainings')->insert([
                'user_id' => $user->id,
                'branch_id' => $branchId,
                'current_page' => 1,
                'completed_pages' => json_encode([]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $trainingProgress = (object)[
                'current_page' => 1,
                'completed_pages' => '[]'
            ];
        }

        if ($page > $trainingProgress->current_page) {
            return redirect()->route('training.show', $trainingProgress->current_page);
        }

        $pageData = DB::table('training_pages')
            ->where('branch_id', $branchId)
            ->where('page_number', $page)
            ->first();

        if (!$pageData) {
            abort(404, 'Page not found in this chapter.');
        }

        return view('training.training', [
            'pageNumber' => $page,
            'currentPage' => $trainingProgress->current_page,
            'completedPages' => json_decode($trainingProgress->completed_pages, true) ?? [],
            'page' => $pageData,
            'branchId' => $branchId,
        ])->with('userRole', $user->role_id);
    }


    public function startChapter(Request $request)
    {
        $request->validate(['branch_id' => 'required|integer']);
        $branchId = $request->branch_id;
        $userId = Auth::id();

        // Set current active branch in session
        session(['active_branch_id' => $branchId]);

        // Check if training progress exists
        $existing = DB::table('trainings')
            ->where('user_id', $userId)
            ->where('branch_id', $branchId)
            ->first();

        // Only insert if not exists
        if (!$existing) {
            DB::table('trainings')->insert([
                'user_id' => $userId,
                'branch_id' => $branchId,
                'current_page' => 1,
                'completed_pages' => json_encode([]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect()->route('training.show', ['page' => 1]);
    }


    public function create()
    {
        return view('training.create');
    }

    public function store(Request $request)
    {
       $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'video_id' => 'nullable|string|max:11',
            'branch_id' => 'required|exists:training_branches,id',
        ]);

        $branchId = $validated['branch_id'];

        $pageNumber = DB::table('training_pages')
            ->where('branch_id', $branchId)
            ->count() + 1;

        $order = DB::table('training_pages')
            ->where('branch_id', $branchId)
            ->count() + 1;
        
        TrainingPage::create([
            ...$validated,
            'page_number' => $pageNumber,
            'order' => $order,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('training.show', $pageNumber)
            ->with('success', 'New page created!');
    }

    public function destroy($branch, $page)
    {
        $trainingPage = TrainingPage::where('branch_id', $branch)
            ->where('page_number', $page)
            ->firstOrFail();
        $trainingPage->delete();

        return redirect()->route('training.show', 1)
            ->with('success', 'Page deleted successfully');
    }

    public function edit($branch, $page)
    {
        $trainingPage = TrainingPage::where('branch_id', $branch)
            ->where('page_number', $page)
            ->firstOrFail();

        return view('training.edit', compact('trainingPage'));
    }

    public function update(Request $request, $page)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'video_id' => 'nullable|string|max:11',
            'branch_id' => 'required|exists:training_branches,id',
        ]);

        TrainingPage::updateOrCreate(
        [
            'page_number' => $page,
            'branch_id' => $validated['branch_id'],
        ],
        $validated
    );

        return redirect()->route('training.show', $page)
            ->with('success', 'Page updated successfully!');
    }

    public function completePage(Request $request)
    {
        $user = Auth::user();
        if (!$user) return redirect()->route('login');

        $branchId = session('active_branch_id');
        if (!$branchId) return redirect()->route('training.chapters')->with('error', 'No active branch.');

        DB::transaction(function () use ($user, $branchId) {
            $currentProgress = DB::table('trainings')
                ->where('user_id', $user->id)
                ->where('branch_id', $branchId)
                ->first();

            $newPage = ($currentProgress->current_page ?? 0) + 1;
            $completedPages = json_decode($currentProgress->completed_pages ?? '[]', true);

            DB::table('trainings')->updateOrInsert(
                ['user_id' => $user->id, 'branch_id' => $branchId],
                [
                    'current_page' => $newPage,
                    'completed_pages' => json_encode(array_unique(array_merge(
                        $completedPages,
                        [$currentProgress->current_page ?? 1]
                    ))),
                    'updated_at' => now()
                ]
            );
        });

        $currentPage = DB::table('trainings')
            ->where('user_id', $user->id)
            ->where('branch_id', $branchId)
            ->value('current_page');

        $pageExists = DB::table('training_pages')
            ->where('branch_id', $branchId)
            ->where('page_number', $currentPage)
            ->exists();

        if (!$pageExists) {
            return redirect()->route('training.chapters')->with('status', 'Chapter completed!');
        }

        return redirect()->route('training.show', ['page' => $currentPage]);
    }



    public function selectChapter()
    {
        $branches = DB::table('training_branches')
            ->orderBy('order')
            ->get();

        $branchPages = [];

        foreach ($branches as $branch) {
            $firstPage = DB::table('training_pages')
                ->where('branch_id', $branch->id)
                ->orderBy('page_number')
                ->first();

            $branchPages[] = [
                'branch' => $branch,
                'first_page' => $firstPage,
            ];
        }

        return view('training.select-chapter', compact('branchPages'));
    }
}