<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;

class ContactsExport implements FromCollection, WithHeadings
{
    protected $contacts;
    protected $months;

    public function __construct(Collection $contacts, array $months)
    {
        $this->contacts = $contacts;
        $this->months = $months;
    }

    public function collection()
    {
        return $this->contacts->map(function ($contact, $index) {
            $index = (int) $index;
            
            $row = [
                'No' => $index + 1,
                'Nama Utusan' => $contact->utusan,
            ];

            foreach ($this->months as $month) {
                $row[$month] = $contact->{$month} ?? 0;
            }

            $row['Total'] = $contact->total;
            $row['Percentage'] = number_format($contact->percentage, 2) . '%';

            return $row;
        });
    }

    public function headings(): array
    {
        $headings = [
            'No',
            'Nama Utusan',
        ];

        foreach ($this->months as $month) {
            $headings[] = $month;
        }

        $headings[] = 'Total';
        $headings[] = 'Percentage';

        return $headings;
    }
}