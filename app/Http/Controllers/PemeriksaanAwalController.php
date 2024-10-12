<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Kunjungan;
use Illuminate\Http\Request;
use App\Models\FotoKunjungan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class PemeriksaanAwalController extends Controller
{
    public function index()
    {
        $query = Kunjungan::with(['pasien', 'fotoKunjungan', 'detailKunjungans.obat']);
        $kunjungans = $query->where('status', 'belum selesai')->get();
        // Fetch riwayat kunjungan for each pasien
        foreach ($kunjungans as $kunjungan) {
            $riwayatKunjungan = Kunjungan::where('id', $kunjungan->pasien_id)
                ->where('id', '!=', $kunjungan->id)
                ->where('status', 'sudah ditangani')
                ->with(['fotoKunjungan', 'detailKunjungans.obat'])
                ->orderBy('tanggal', 'desc')
                ->get();
            $kunjungan->riwayatKunjungan = $riwayatKunjungan;
        }
        return view('pages.pemeriksaan-awal', compact('kunjungans'));
    }
    public function update(Request $request, Kunjungan $kunjungan)
    {
        try {
            $validatedData = $request->validate([
                'pemeriksaan_awal' => 'required',
            ]);

            $kunjungan->pemeriksaan_awal = $validatedData['pemeriksaan_awal'];
            $kunjungan->status = 'selesai pemeriksaan awal';
            $kunjungan->save();

            return redirect()->route('pemeriksaan_awal.index')->with('success', 'Pemeriksaan awal berhasil disimpan dan status diperbarui.');
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Gagal memperbarui pemeriksaan awal: ' . $e->getMessage())->withInput();
        }
    }
    public function uploadFoto(Request $request, Kunjungan $kunjungan)
    {
        $request->validate([
            'foto.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'nama_foto.*' => 'required|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            if ($request->hasFile('foto')) {
                $uploadPath = public_path('fotoKunjungan');

                if (!File::isDirectory($uploadPath)) {
                    File::makeDirectory($uploadPath, 0777, true, true);
                }

                foreach ($request->file('foto') as $key => $foto) {
                    $fileName = time() . '_' . $foto->getClientOriginalName();
                    $foto->move($uploadPath, $fileName);

                    FotoKunjungan::create([
                        'kunjungan_id' => $kunjungan->id,
                        'nama' => $request->nama_foto[$key],
                        'foto' => 'fotoKunjungan/' . $fileName
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('pemeriksaan_awal.index')->with('success', 'Foto berhasil diunggah.');
        } catch (Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat mengunggah foto: ' . $e->getMessage())->withInput();
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
}
