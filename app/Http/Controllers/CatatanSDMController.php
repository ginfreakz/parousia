<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CatatanSdmExport;

class CatatanSDMController extends Controller
{
    public function index()
    {
        $sdm = DB::table('sdm')->get();
        return view('sdm.catatan-sdm', compact('sdm'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'kategori_sdm' => 'required',
        ]);

        DB::table('catatan_sdm')->insert([
            'sdm_id' => $request->name,
            'kategori_sdm' => $validatedData['kategori_sdm'],
            'tanggal' => $request->tanggal,
            'catatan' => $request->catatan,
        ]);

        return redirect()->back()->with('success', 'Catatan SDM berhasil disimpan.');
    }

    public function catatanSDMLists()
    {
        $user = auth()->user();
        $userRole = $user->role_id;

        $catatan_sdm = DB::table('catatan_sdm')
        ->join('sdm', 'sdm.id', '=', 'catatan_sdm.sdm_id')
        ->select('catatan_sdm.*', 'sdm.nama_lengkap as sdm_name')
        ->get();

        return view('sdm.catatan-sdm-show', ['catatan_sdm' => $catatan_sdm])->with([
            'userRole' => $userRole,
        ]);
    }

    public function filter(Request $request)
    {
        $user = auth()->user();
        $userRole = $user->role_id;

        $query = DB::table('catatan_sdm')
        ->join('sdm', 'sdm.id', '=', 'catatan_sdm.sdm_id')
        ->select('catatan_sdm.*', 'sdm.nama_lengkap as sdm_name');

        if ($request->filled('name')) {
            $query->where('sdm.nama_lengkap', 'ilike', '%' . $request->name . '%');
        }

        if ($request->filled('kategori')) {
            $query->where('catatan_sdm.kategori_sdm', $request->kategori);
        }

        $catatan_sdm = $query->get();

        return response()->json([
            'catatan_sdm' => $catatan_sdm,
            'userRole' => $userRole,
        ]);
    }

    public function edit($id)
    {
        $catatan = DB::table('catatan_sdm')->where('id', $id)->first();
        $sdm = DB::table('sdm')->get();
        return view('sdm.edit-catatan-sdm', compact('catatan', 'sdm'));
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'sdm_id' => 'required',
            'kategori_sdm' => 'required',
            'catatan' => 'required|string',
        ]);

        $updateData = [
            'sdm_id' => $validatedData['sdm_id'],
            'kategori_sdm' => $validatedData['kategori_sdm'],
            'catatan' => $validatedData['catatan'],
            'tanggal' => $request->tanggal,
        ];

        // if ($validatedData['kategori_sdm'] == 1) {
        //     $updateData['tempat'] = $request->tempat;
        // } elseif ($validatedData['kategori_sdm'] == 2) {
        //     $updateData['jenis_penyakit'] = $request->jenis_penyakit;
        //     $updateData['tanggal'] = $request->tanggal;
        // } elseif ($validatedData['kategori_sdm'] == 3) {
        //     $updateData['tanggal'] = $request->tanggal;
        //     $updateData['topik'] = $request->topik;
        // }

        DB::table('catatan_sdm')->where('id', $id)->update($updateData);

        return redirect()->route('catatan-sdm.lists')->with('success', 'Catatan SDM updated successfully!');
    }

    public function destroy($id)
    {
        DB::table('catatan_sdm')->where('id', $id)->delete();
        return redirect()->back()->with('success', 'Catatan SDM deleted successfully!');
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(new CatatanSdmExport($request), 'catatan-sdm.xlsx');
    }
}