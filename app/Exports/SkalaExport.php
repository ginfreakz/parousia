<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SkalaExport implements FromCollection, WithHeadings
{
    protected $skalaData;
    protected $months;

    public function __construct(Collection $skalaData, array $months)
    {
        $this->skalaData = $skalaData;
        $this->months = $months;
    }

    public function headings(): array
    {
        $headings = ['No', 'Skala'];

        foreach ($this->months as $month) {
            $headings[] = $month;
        }

        $headings[] = 'Total';
        $headings[] = 'Percentage';

        return $headings;
    }

    public function collection()
    {
        $sortedData = $this->skalaData->sortBy('skala')->values(); // Sort by skala

        return $sortedData->map(function ($data, $index) {
            $row = [
                'No' => $index + 1, // Ensure sequential numbering
                'Skala' => $data->skala,
            ];

            foreach ($this->months as $month) {
                $row[$month] = $data->{$month} ?? 0;
            }

            $row['Total'] = $data->total;
            $row['Percentage'] = number_format((float) $data->percentage, 2) . '%';

            return (object) $row;
        });
    }
}