<?php

namespace App\Http\Controllers;

use App\Models\Kunjungan;
use Illuminate\Http\Request;

class RiwayatController extends Controller
{
    public function index()
    {
        $kunjungans = Kunjungan::with(['pasien', 'detailKunjungans.obat', 'fotoKunjungan'])
            ->where('status', 'selesai')
            ->orderBy('tanggal', 'desc')->get();

        return view('pages.riwayat', compact('kunjungans'));
    }
}
