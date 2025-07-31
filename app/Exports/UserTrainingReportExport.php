<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UserTrainingReportExport implements FromCollection, WithHeadings
{
    protected $name;

    public function __construct($name = null)
    {
        $this->name = $name;
    }

    public function collection()
    {
        // Match: only count pages with valid branch_id
        $totalGlobalPages = DB::table('training_pages')
            ->whereNotNull('branch_id')
            ->count();

        // Fetch user training data
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
            ->when($this->name, function ($query) {
                $query->where('u.name', 'ilike', '%' . $this->name . '%');
            })
            ->orderBy('u.name')
            ->get();

        // Format the report
        return $trainings->map(function ($item) use ($totalGlobalPages) {
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
    }

    public function headings(): array
    {
        return [
            'User Name',
            'Branch',
            'Current Page',
            'Total Pages',
            'Completed Pages',
            'Progress %',
        ];
    }
}
