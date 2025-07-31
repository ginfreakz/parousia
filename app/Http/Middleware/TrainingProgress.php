<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TrainingProgress
{
    public function handle($request, Closure $next)
    {
        $user = Auth::user();
        $requestedPage = (int) $request->route('page');

        // Get or create training progress
        $training = DB::table('trainings')
            ->where('user_id', $user->id)
            ->first();

        if (!$training) {
            DB::table('trainings')->insert([
                'user_id' => $user->id,
                'current_page' => 1,
                'completed_pages' => json_encode([])
            ]);
            return redirect()->route('training.show', 1);
        }

        if ($requestedPage > $training->current_page) {
            return redirect()->route('training.show', $training->current_page);
        }

        return $next($request);
    }
}