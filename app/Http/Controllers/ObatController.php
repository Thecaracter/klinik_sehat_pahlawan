<?php

namespace App\Http\Controllers;

use App\Models\Obat;
use Illuminate\Http\Request;
use Exception;
use Log;

class ObatController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Obat::query();

            if ($request->has('search')) {
                $searchTerm = $request->search;
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('kode_obat', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('merk', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('nama', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('jenis', 'LIKE', "%{$searchTerm}%");
                });
            }

            $obat = $query->paginate(10);

            $satuanOptions = ['Tablet', 'Kapsul', 'Botol', 'Ampul', 'Vial', 'Tube', 'Sachet', 'Strip'];

            // Ambil kode obat terakhir
            $lastObat = Obat::orderBy('kode_obat', 'desc')->first();
            $lastKodeObat = $lastObat ? $lastObat->kode_obat : 'OBT0000';

            // Generate kode obat berikutnya
            $nextKodeObat = 'OBT' . str_pad((intval(substr($lastKodeObat, 3)) + 1), 4, '0', STR_PAD_LEFT);

            return view('pages.obat', compact('obat', 'satuanOptions', 'nextKodeObat'));
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return redirect()->route('obat.index')->with('error', 'Gagal mengambil data obat: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'merk' => 'required',
                'nama' => 'required',
                'jenis' => 'required',
                'kegunaan' => 'required',
                'harga' => 'required|integer',
                'satuan' => 'required',
            ]);

            $kodeObat = $this->generateKodeObat();

            Obat::create(array_merge(
                $request->all(),
                ['kode_obat' => $kodeObat]
            ));

            return redirect()->route('obat.index')->with('success', 'Obat berhasil ditambahkan');
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return redirect()->route('obat.index')->with('error', 'Gagal menambahkan obat: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $obat = Obat::findOrFail($id);

            $request->validate([
                'merk' => 'required',
                'nama' => 'required',
                'jenis' => 'required',
                'kegunaan' => 'required',
                'harga' => 'required|integer',
                'satuan' => 'required',
            ]);

            $obat->update($request->except('kode_obat'));

            return redirect()->route('obat.index')->with('success', 'Obat berhasil diperbarui');
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return redirect()->route('obat.index')->with('error', 'Gagal memperbarui obat: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $obat = Obat::findOrFail($id);
            $obat->delete();

            return redirect()->route('obat.index')->with('success', 'Obat berhasil dihapus');
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return redirect()->route('obat.index')->with('error', 'Gagal menghapus obat: ' . $e->getMessage());
        }
    }

    private function generateKodeObat()
    {
        $lastObat = Obat::orderBy('kode_obat', 'desc')->first();
        if (!$lastObat) {
            return 'OBT0001';
        }

        $lastNumber = intval(substr($lastObat->kode_obat, 3));
        $newNumber = $lastNumber + 1;
        return 'OBT' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
}