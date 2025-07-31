<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SdmReportExport implements FromCollection, WithHeadings
{
    protected $lembagaId;

    public function __construct($lembagaId = null)
    {
        $this->lembagaId = $lembagaId;
    }

    public function collection()
    {
        $query = DB::table('sdm')
            ->select('sdm.lembaga_id', 'lembaga.name as nama_lembaga', 'sdm.kategori_sdm', DB::raw('COUNT(*) as total'))
            ->join('lembaga', 'lembaga.id', '=', 'sdm.lembaga_id')
            ->groupBy('sdm.lembaga_id', 'lembaga.name', 'sdm.kategori_sdm')
            ->orderBy('lembaga.name');

        if ($this->lembagaId && $this->lembagaId != 0) {
            $query->where('sdm.lembaga_id', $this->lembagaId);
        }

        $results = $query->get();

        $rows = $results->map(function ($row, $index) {
            return [
                'no' => $index + 1,
                'nama_lembaga' => $row->nama_lembaga,
                'kategori_sdm' => $row->kategori_sdm,
                'total' => $row->total,
            ];
        });

        $beasiswaCount = DB::table('sdm')
            ->where('pendidikan->beasiswa_parousia->status', 'Ya')
            ->when($this->lembagaId && $this->lembagaId != 0, function ($q) {
                $q->where('lembaga_id', $this->lembagaId);
            })
            ->count();

        $rows->push([
            'no' => '',
            'nama_lembaga' => '',
            'kategori_sdm' => 'Total Beasiswa Parousia',
            'total' => $beasiswaCount,
        ]);

        return $rows;
    }

    public function headings(): array
    {
        return ['No', 'Nama Lembaga', 'Kategori SDM', 'Total'];
    }
}
