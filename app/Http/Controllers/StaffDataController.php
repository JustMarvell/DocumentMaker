<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StaffData;

class StaffDataController extends Controller
{

    public function index() {
        return response()->json(StaffData::orderBy('staff_name')->get());
    }

    public function store(Request $request) {
        // return as JSON derulo.....
        $request->validate([
            'staff_name' => 'required|string|max:255',
            'nip' => 'required|string|unique:staff_data,nip,',
            'email' => 'required|email',
            'work_unit' => 'required|string|max:255',
            'rank' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:20',
        ]);

        StaffData::create($request->only([
            'staff_name',
            'nip',
            'email',
            'phone_number',
            'rank',
            'position',
            'work_unit',
        ]));

        return back()->with('success', "Data staff {$request->staff_name} berhasil ditambahkan.");
    }

    public function update(Request $request , StaffData $staffDatum) {
        $request->validate([
            'staff_name' => 'required|string|max:255',
            'nip' => 'required|string|unique:staff_data,nip' . $staffDatum->id,
            'email' => 'required|email',
            'work_unit' => 'required|string|max:255',
            'rank' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
        ]);

        $staffDatum->update($request->only([
            'staff_name', 'nip', 'email',
            'phone_number', 'rank', 'position', 'work_unit',
        ]));

        return back()->with('success', "Data staff {$staffDatum->staff_name} berhasil diperbaharui");
    }

    public function destroy(StaffData $staffDatum) {
        $name = $staffDatum->staff_name;
        $staffDatum->delete();

        return back()->with('success', "Data staff {$name} berhasil dihapus.");
    }
}
