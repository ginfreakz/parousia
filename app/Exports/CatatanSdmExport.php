<?php

namespace App\Exports;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CatatanSdmExport implements FromCollection, WithHeadings
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = DB::table('catatan_sdm')
            ->join('sdm', 'sdm.id', '=', 'catatan_sdm.sdm_id')
            ->select('sdm.nama_lengkap as sdm_name', 'catatan_sdm.kategori_sdm', 'catatan_sdm.tanggal', 'catatan_sdm.catatan');

        if ($this->request->filled('name')) {
            $query->where('sdm.nama_lengkap', 'ilike', '%' . $this->request->name . '%');
        }

        if ($this->request->filled('kategori')) {
            $query->where('catatan_sdm.kategori_sdm', $this->request->kategori);
        }

        $results = $query->get();

        return $results->map(function ($row, $index) {
            $kategori = (int) $row->kategori_sdm;

            $kategoriLabel = match ($kategori) {
                1 => 'Pelayanan',
                2 => 'Kesehatan',
                default => 'Lain - Lain',
            };

            return [
                'no' => $index + 1,
                'sdm_name' => $row->sdm_name,
                'kategori_sdm' => $kategoriLabel,
                'tanggal' => $row->tanggal,
                'catatan' => $row->catatan,
            ];
        });
    }

    public function headings(): array
    {
        return ['No', 'Nama SDM', 'Kategori SDM', 'Tanggal', 'Catatan'];
    }
}
