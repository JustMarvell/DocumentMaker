<?php

namespace App\Http\Controllers;

use App\Models\OfficialData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
            'signature_image' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        $data = $request->only(['staff_name', 'nip', 'email', 'phone_number', 'rank', 'position', 'work_unit']);

        if ($request->hasFile('signature_image')) {
            $data['signature_image'] = $request->file('signature_image')
                ->store('', 'signatures');
        }

        OfficialData::create($data);

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
            'signature_image' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        $data = $request->only(['staff_name', 'nip', 'email', 'phone_number', 'rank', 'position', 'work_unit']);

        if ($request->hasFile('signature_image')) {
            // Delete old image
            if ($officialDatum->signature_image) {
                Storage::disk('signatures')->delete($officialDatum->signature_image);
            }
            $data['signature_image'] = $request->file('signature_image')
                ->store('', 'signatures');
        }

        $officialDatum->update($data);

        return back()->with('success', "Data pejabat {$officialDatum->staff_name} berhasil diperbarui.");
    }

    public function destroy(OfficialData $officialDatum)
    {
        // Delete signature image from storage
        if ($officialDatum->signature_image) {
            Storage::disk('signatures')->delete($officialDatum->signature_image);
        }

        $name = $officialDatum->staff_name;
        $officialDatum->delete();
        return back()->with('success', "Data pejabat {$name} berhasil dihapus.");
    }

    public function deleteSignatureImage(OfficialData $officialDatum)
    {
        if ($officialDatum->signature_image) {
            Storage::disk('signatures')->delete($officialDatum->signature_image);
            $officialDatum->update(['signature_image' => null]);
        }
        return back()->with('success', "Gambar tanda tangan {$officialDatum->staff_name} berhasil dihapus.");
    }

    public function serveSignature(string $filename)
    {
        abort_unless(auth()->check() && auth()->user()->isAdmin(), 403);

        $path = storage_path('app/signatures/' . basename($filename));
        abort_unless(file_exists($path), 404);
        return response()->file($path);
    }
}