<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Obat;
use App\Models\Kunjungan;
use Illuminate\Http\Request;
use App\Models\FotoKunjungan;
use App\Models\DetailKunjungan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class PemeriksaanController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $role = $user->role;

        $query = Kunjungan::with(['pasien', 'fotoKunjungan', 'detailKunjungans.obat']);

        if ($role === 'dokter') {
            $query->where('ditangani_oleh', 'dokter');
        } elseif ($role === 'bidan') {
            $query->where('ditangani_oleh', 'bidan');
        }

        $kunjungans = $query->where('status', 'selesai pemeriksaan awal')->get();
        $obats = Obat::all();

        // Fetch riwayat kunjungan for each pasien
        foreach ($kunjungans as $kunjungan) {
            $riwayatKunjungan = Kunjungan::where('pasien_id', $kunjungan->pasien_id)
                ->where('id', '!=', $kunjungan->id)
                ->where('status', 'sudah ditangani')
                ->with(['fotoKunjungan', 'detailKunjungans.obat'])
                ->orderBy('tanggal', 'desc')
                ->get();
            $kunjungan->riwayatKunjungan = $riwayatKunjungan;
        }

        return view('pages.pemeriksaan', compact('kunjungans', 'obats'));
    }

    public function update(Request $request, Kunjungan $kunjungan)
    {
        DB::beginTransaction();

        try {
            $validatedData = $request->validate([
                'pemeriksaan_awal' => 'nullable|string',
                'diagnosa' => 'required|string',
                'tindakan' => 'required|string',
                'obat_id' => 'array',
                'obat_id.*' => 'exists:obat,id',
                'jumlah_obat' => 'array',
                'jumlah_obat.*' => 'numeric|min:0',
                'instruksi' => 'array',
                'instruksi.*' => 'string',
            ]);

            $kunjungan->update([
                'diagnosa' => $validatedData['diagnosa'],
                'tindakan' => $validatedData['tindakan'],
                'status' => 'sudah ditangani',
                'user_id' => Auth::id()
            ]);

            // Restore stock for existing detail kunjungan records
            foreach ($kunjungan->detailKunjungans as $existingDetail) {
                $obat = Obat::findOrFail($existingDetail->id_obat);
                $obat->tambahStok($existingDetail->jumlah_obat);
            }

            // Delete old detail kunjungan records
            $kunjungan->detailKunjungans()->delete();

            // Add new detail kunjungan records and reduce stock
            if (isset($validatedData['obat_id'])) {
                foreach ($validatedData['obat_id'] as $key => $obatId) {
                    $obat = Obat::findOrFail($obatId);
                    $jumlahObat = (float) $validatedData['jumlah_obat'][$key];

                    // Ensure the stock is sufficient
                    if ($obat->stok < $jumlahObat) {
                        throw new Exception("Stok obat {$obat->nama} tidak mencukupi. Stok tersedia: {$obat->formatStok()}");
                    }

                    // Reduce stock
                    $obat->kurangiStok($jumlahObat);

                    // Create new DetailKunjungan record
                    DetailKunjungan::create([
                        'id_kunjungan' => $kunjungan->id,
                        'id_obat' => $obatId,
                        'jumlah_obat' => $jumlahObat,
                        'instruksi' => $validatedData['instruksi'][$key],
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('pemeriksaan.index')->with('success', 'Pemeriksaan berhasil diperbarui dan stok obat telah disesuaikan.');
        } catch (Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
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
            return redirect()->route('pemeriksaan.index')->with('success', 'Foto berhasil diunggah.');
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

    public function deleteObat(DetailKunjungan $detailKunjungan)
    {
        try {
            $detailKunjungan->delete();
            return redirect()->back()->with('success', 'Obat berhasil dihapus dari kunjungan.');
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus obat: ' . $e->getMessage());
        }
    }
}
