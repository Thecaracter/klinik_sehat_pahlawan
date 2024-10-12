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
                $query->where(function ($q) use ($search) {
                    $q->where('nik', 'LIKE', "%{$search}%")
                        ->orWhere('nama', 'LIKE', "%{$search}%")
                        ->orWhere('alamat', 'LIKE', "%{$search}%");
                });
            }

            $pasiens = $query->paginate(10);

            return view('pages.pasien', compact('pasiens'));
        } catch (Exception $e) {
            Log::error('Error fetching pasiens: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat mengambil data pasien.');
        }
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'nik' => 'nullable|string|unique:pasien,nik|max:16',
                'nama' => 'required|string|max:255',
                'tanggal_lahir' => 'required|date',
                'alamat' => 'required|string',
                'no_hp' => 'required|string|max:15',
            ]);

            $pasien = Pasien::create($validatedData);

            Log::info('New pasien created: ' . $pasien->id);
            return redirect()->route('pasiens.index')->with('success', 'Pasien berhasil ditambahkan.');
        } catch (Exception $e) {
            Log::error('Error creating pasien: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan saat menambahkan pasien.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $pasien = Pasien::findOrFail($id);

            $validatedData = $request->validate([
                'nik' => 'nullable|string|unique:pasien,nik,' . $id . ',id|max:16',
                'nama' => 'required|string|max:255',
                'tanggal_lahir' => 'required|date',
                'alamat' => 'required|string',
                'no_hp' => 'required|string|max:15',
            ]);

            $pasien->update($validatedData);

            Log::info('Pasien updated: ' . $id);
            return redirect()->route('pasiens.index')->with('success', 'Data pasien berhasil diperbarui.');
        } catch (Exception $e) {
            Log::error('Error updating pasien: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui data pasien.');
        }
    }

    public function destroy($id)
    {
        try {
            $pasien = Pasien::findOrFail($id);
            $pasien->delete();

            Log::info('Pasien deleted: ' . $id);
            return redirect()->route('pasiens.index')->with('success', 'Data pasien berhasil dihapus.');
        } catch (Exception $e) {
            Log::error('Error deleting pasien: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menghapus data pasien.');
        }
    }
}