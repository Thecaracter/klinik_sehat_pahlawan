<?php

namespace App\Http\Controllers;


use Exception;
use App\Models\Obat;
use App\Models\ObatMasuk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ObatMasukController extends Controller
{
    public function index(Request $request)
    {
        $query = ObatMasuk::with('obat');

        if ($request->has('start_date') && $request->has('end_date')) {
            $start_date = $request->start_date;
            $end_date = $request->end_date;
            $query->whereBetween('created_at', [$start_date, $end_date]);
        }
        $query->orderBy('created_at', 'desc');

        $obatmasuk = $query->paginate(10);
        $obat = Obat::all();

        return view('pages.obat-masuk', compact('obatmasuk', 'obat'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'obat_id' => 'required|exists:obat,id',
            'nomor_batch' => 'required|string',
            'jumlah' => 'required|numeric|min:0',
            'harga_beli' => 'required|numeric|min:0',
            'tanggal_kadaluarsa' => 'required|date',
        ]);

        DB::beginTransaction();
        try {
            $obatmasuk = ObatMasuk::create($request->all());

            $obat = Obat::findOrFail($request->obat_id);
            $obat->tambahStok($request->jumlah);

            DB::commit();
            return redirect()->route('obatmasuk.index')->with('success', 'Data obat masuk berhasil ditambahkan dan stok obat diperbarui.');
        } catch (Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }
    public function update(Request $request, Obatmasuk $obatmasuk)
    {
        $request->validate([
            'obat_id' => 'required|exists:obat,id',
            'nomor_batch' => 'required|string',
            'jumlah' => 'required|numeric|min:0',
            'harga_beli' => 'required|numeric|min:0',
            'tanggal_kadaluarsa' => 'required|date',
        ]);

        DB::beginTransaction();
        try {
            $oldJumlah = $obatmasuk->jumlah;
            $newJumlah = $request->jumlah;

            $obatmasuk->update($request->all());

            $obat = Obat::findOrFail($request->obat_id);
            if ($oldJumlah != $newJumlah) {
                $selisih = $newJumlah - $oldJumlah;
                if ($selisih > 0) {
                    $obat->tambahStok($selisih);
                } else {
                    $obat->kurangiStok(abs($selisih));
                }
            }

            DB::commit();
            return redirect()->route('obatmasuk.index')->with('success', 'Data obat masuk berhasil diperbarui dan stok obat disesuaikan.');
        } catch (Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Obatmasuk $obatmasuk)
    {
        DB::beginTransaction();
        try {
            $obat = $obatmasuk->obat;
            $jumlah = $obatmasuk->jumlah;

            $obatmasuk->delete();
            $obat->kurangiStok($jumlah);

            DB::commit();
            return redirect()->route('obatmasuk.index')->with('success', 'Data obat masuk berhasil dihapus dan stok obat dikurangi.');
        } catch (Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}