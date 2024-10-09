<?php

namespace App\Http\Controllers;

use App\Models\Kunjungan;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PembayaranController extends Controller
{
    public function index()
    {
        $kunjungans = Kunjungan::with(['pasien', 'detailKunjungans.obat'])
            ->where('status', 'sudah ditangani')
            ->orderBy('tanggal', 'desc')
            ->get();

        return view('pages.pembayaran', compact('kunjungans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kunjungan_id' => 'required|exists:kunjungan,id',
            'total_bayar' => 'required|numeric|min:0',
            'metode_pembayaran' => 'required|string',
        ]);

        DB::beginTransaction();

        try {
            $pembayaran = Pembayaran::create([
                'kunjungan_id' => $request->kunjungan_id,
                'total_bayar' => $request->total_bayar,
                'metode_pembayaran' => $request->metode_pembayaran,
            ]);

            $kunjungan = Kunjungan::findOrFail($request->kunjungan_id);
            $kunjungan->update(['status' => 'selesai']);

            DB::commit();

            return redirect()->route('pembayaran.index')->with('success', 'Pembayaran berhasil disimpan dan status kunjungan diperbarui.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }
}
