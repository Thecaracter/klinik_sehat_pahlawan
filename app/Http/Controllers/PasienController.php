<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Pasien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PasienController extends Controller
{
    public function index(Request $request)
    {
        try {
            $search = $request->input('search');
            $query = Pasien::query()->orderBy('created_at', 'desc');

            if ($search) {
                $query->where('nik', 'LIKE', "%{$search}%")
                    ->orWhere('nama', 'LIKE', "%{$search}%")
                    ->orWhere('alamat', 'LIKE', "%{$search}%");
            }

            $pasiens = $query->paginate(10);
            $satuanOptions = ['Tablet', 'Kapsul', 'Botol', 'Ampul', 'Vial', 'Tube', 'Sachet', 'Strip'];

            return view('pages.pasien', compact('pasiens', 'satuanOptions'));
        } catch (Exception $e) {
            Log::error('Error fetching pasiens: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat mengambil data pasien.');
        }
    }


    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'nik' => 'required|string|unique:pasien,nik|max:16',
                'nama' => 'required|string|max:255',
                'tanggal_lahir' => 'required|date',
                'alamat' => 'required|string',
                'no_hp' => 'required|string|max:15',
            ]);

            Pasien::create($validatedData);

            Log::info('New pasien created: ' . $validatedData['nik']);
            return redirect()->route('pasiens.index')->with('success', 'Pasien berhasil ditambahkan.');
        } catch (Exception $e) {
            Log::error('Error creating pasien: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan saat menambahkan pasien.');
        }
    }

    public function update(Request $request, $nik)
    {
        try {
            $pasien = Pasien::findOrFail($nik);

            $validatedData = $request->validate([
                'nama' => 'required|string|max:255',
                'tanggal_lahir' => 'required|date',
                'alamat' => 'required|string',
                'no_hp' => 'required|string|max:15',
            ]);

            $pasien->update($validatedData);

            Log::info('Pasien updated: ' . $nik);
            return redirect()->route('pasiens.index')->with('success', 'Data pasien berhasil diperbarui.');
        } catch (Exception $e) {
            Log::error('Error updating pasien: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui data pasien.');
        }
    }

    public function destroy($nik)
    {
        try {
            $pasien = Pasien::findOrFail($nik);
            $pasien->delete();

            Log::info('Pasien deleted: ' . $nik);
            return redirect()->route('pasiens.index')->with('success', 'Data pasien berhasil dihapus.');
        } catch (Exception $e) {
            Log::error('Error deleting pasien: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menghapus data pasien.');
        }
    }
}
