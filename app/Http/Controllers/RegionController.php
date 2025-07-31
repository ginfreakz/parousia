<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Region;

class RegionController extends Controller
{
    public function index()
    {
        $regions = Region::all();
        return view('misionaris.region-list', compact('regions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kota' => 'required|string|max:255',
            'provinsi' => 'required|string|max:255',
        ]);

        $lastInsertedId = Region::latest()->first()->id ?? 0;
        $newId = $lastInsertedId + 1; 
        Region::create([
            'id' => $newId,
            'kota' => $request->kota,
            'provinsi' => $request->provinsi,
        ]);

        return redirect()->back()->with('status', 'Region created successfully!');
    }

    public function edit($id)
    {
        $region = Region::findOrFail($id);
        return view('misionaris.edit-region', compact('region'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kota' => 'required|string|max:255',
            'provinsi' => 'required|string|max:255',
        ]);

        $region = Region::findOrFail($id);
        $region->update([
            'kota' => $request->kota,
            'provinsi' => $request->provinsi,
        ]);

        return redirect()->route('region.index')->with('status', 'Region updated successfully!');
    }

    public function destroy($id)
    {
        $region = Region::findOrFail($id);
        $region->delete();

        return redirect()->route('region.index')->with('status', 'Region deleted successfully!');
    }
}