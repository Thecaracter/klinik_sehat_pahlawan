<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Pasien;
use App\Models\Kunjungan;
use Illuminate\Http\Request;
use App\Models\FotoKunjungan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class KunjunganController extends Controller
{
    public function index()
    {
        $kunjungans = Kunjungan::where('status', 'belum selesai')->with('fotoKunjungan', 'pasien')->get();
        return view('pages.kunjungan', compact('kunjungans'));
    }

    public function checkNik(Request $request)
    {
        $request->validate([
            'nik' => 'required|string'
        ]);

        $nik = $request->input('nik');
        $pasien = Pasien::where('nik', $nik)->first();

        if ($pasien) {
            return redirect()->route('kunjungan.index')
                ->with('showCreateModal', true)
                ->with('nik', $nik);
        } else {
            return redirect()->route('pasiens.index');
        }
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'pasien_nik' => 'required|string',
                'ditangani_oleh' => 'required|in:dokter,bidan',
                'tanggal' => 'required|date',
                'keluhan' => 'required|string',
                'foto_kunjungan.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            $validatedData['user_id'] = Auth::id();
            $validatedData['status'] = 'belum selesai';

            $kunjungan = Kunjungan::create($validatedData);

            if ($request->hasFile('foto_kunjungan')) {
                $uploadPath = public_path('fotoKunjungan');

                if (!File::isDirectory($uploadPath)) {
                    File::makeDirectory($uploadPath, 0777, true, true);
                }

                foreach ($request->file('foto_kunjungan') as $foto) {
                    $fileName = time() . '_' . $foto->getClientOriginalName();
                    $foto->move($uploadPath, $fileName);

                    FotoKunjungan::create([
                        'kunjungan_id' => $kunjungan->id,
                        'nama' => $foto->getClientOriginalName(),
                        'foto' => 'fotoKunjungan/' . $fileName
                    ]);
                }
            }

            return redirect()->route('kunjungan.index')->with('success', 'Kunjungan berhasil ditambahkan.');
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Gagal menambahkan kunjungan: ' . $e->getMessage())->withInput();
        }
    }

    public function update(Request $request, Kunjungan $kunjungan)
    {
        try {
            $validatedData = $request->validate([
                'pasien_nik' => 'required|string',
                'ditangani_oleh' => 'required|in:dokter,bidan',
                'tanggal' => 'required|date',
                'keluhan' => 'required|string',
                'foto_kunjungan.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            $kunjungan->update($validatedData);

            if ($request->hasFile('foto_kunjungan')) {
                $uploadPath = public_path('fotoKunjungan');

                if (!File::isDirectory($uploadPath)) {
                    File::makeDirectory($uploadPath, 0777, true, true);
                }

                foreach ($request->file('foto_kunjungan') as $foto) {
                    $fileName = time() . '_' . $foto->getClientOriginalName();
                    $foto->move($uploadPath, $fileName);

                    FotoKunjungan::create([
                        'kunjungan_id' => $kunjungan->id,
                        'nama' => $foto->getClientOriginalName(),
                        'foto' => 'fotoKunjungan/' . $fileName
                    ]);
                }
            }

            return redirect()->route('kunjungan.index')->with('success', 'Kunjungan berhasil diperbarui.');
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Gagal memperbarui kunjungan: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Kunjungan $kunjungan)
    {
        try {
            // Hapus semua foto terkait
            foreach ($kunjungan->fotoKunjungan as $foto) {
                $filePath = public_path($foto->foto);
                if (File::exists($filePath)) {
                    File::delete($filePath);
                }
                $foto->delete();
            }

            // Hapus kunjungan
            $kunjungan->delete();

            return redirect()->route('kunjungan.index')->with('success', 'Kunjungan dan semua foto terkait berhasil dihapus.');
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus kunjungan: ' . $e->getMessage());
        }
    }

    public function deleteFoto(FotoKunjungan $foto)
    {
        try {
            $filePath = public_path($foto->foto);
            if (File::exists($filePath)) {
                File::delete($filePath);
            }
            $foto->delete();
            return redirect()->back()->with('success', 'Foto berhasil dihapus.');
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus foto: ' . $e->getMessage());
        }
    }
    public function addPhoto(Request $request, Kunjungan $kunjungan)
    {
        try {
            $request->validate([
                'nama_foto' => 'required|string',
                'foto' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            $uploadPath = public_path('fotoKunjungan');

            if (!File::isDirectory($uploadPath)) {
                File::makeDirectory($uploadPath, 0777, true, true);
            }

            $foto = $request->file('foto');
            $fileName = time() . '_' . $foto->getClientOriginalName();
            $foto->move($uploadPath, $fileName);

            FotoKunjungan::create([
                'kunjungan_id' => $kunjungan->id,
                'nama' => $request->nama_foto,
                'foto' => 'fotoKunjungan/' . $fileName
            ]);

            return redirect()->route('kunjungan.index')->with('success', 'Foto berhasil ditambahkan.');
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Gagal menambahkan foto: ' . $e->getMessage());
        }
    }
}