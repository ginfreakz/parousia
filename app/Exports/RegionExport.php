<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RegionExport implements FromCollection, WithHeadings
{
    protected $regionData;
    protected $months;

    public function __construct(Collection $regionData, array $months)
    {
        $this->regionData = $regionData;
        $this->months = $months;
    }

    public function collection()
    {
        $sortedData = $this->regionData->sortBy('Lokasi')->values(); // Sort by location name

        return $sortedData->map(function ($data, $index) {
            $row = [
                'No' => $index + 1, // Ensure sequential numbering
                'Lokasi' => $data->Lokasi,
            ];

            foreach ($this->months as $month) {
                $row[$month] = $data->{$month} ?? 0;
            }

            $row['Total'] = $data->Total;
            $row['Percentage'] = number_format((float) $data->Percentage, 2) . '%';

            return (object) $row;
        });
    }

    public function headings(): array
    {
        $headings = ['No', 'Lokasi'];

        foreach ($this->months as $month) {
            $headings[] = $month;
        }

        $headings[] = 'Total';
        $headings[] = 'Percentage';

        return $headings;
    }
}