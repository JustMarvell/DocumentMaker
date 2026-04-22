<?php

namespace App\Http\Controllers;

use App\Models\OfficialData;
use Illuminate\Http\Request;

class OfficialDataController extends Controller
{
    public function index()
    {
        return response()->json(OfficialData::orderBy('staff_name')->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'staff_name' => 'required|string|max:255',
            'nip' => 'required|string|unique:official_data,nip',
            'email' => 'required|email|unique:official_data,email',
            'work_unit' => 'required|string|max:255',
            'rank' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:20',
        ]);

        OfficialData::create($request->only([
            'staff_name',
            'nip',
            'email',
            'phone_number',
            'rank',
            'position',
            'work_unit',
        ]));

        return back()->with('success', "Data pejabat {$request->staff_name} berhasil ditambahkan.");
    }

    public function update(Request $request, OfficialData $officialDatum)
    {
        $request->validate([
            'staff_name' => 'required|string|max:255',
            'nip' => 'required|string|unique:official_data,nip,' . $officialDatum->id,
            'email' => 'required|email|unique:official_data,email,' . $officialDatum->id,
            'work_unit' => 'required|string|max:255',
            'rank' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:20',
        ]);

        $officialDatum->update($request->only([
            'staff_name',
            'nip',
            'email',
            'phone_number',
            'rank',
            'position',
            'work_unit',
        ]));

        return back()->with('success', "Data pejabat {$officialDatum->staff_name} berhasil diperbarui.");
    }

    public function destroy(OfficialData $officialDatum)
    {
        $name = $officialDatum->staff_name;
        $officialDatum->delete();
        return back()->with('success', "Data pejabat {$name} berhasil dihapus.");
    }
}