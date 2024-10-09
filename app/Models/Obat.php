<?php

namespace App\Models;

use Exception;
use App\Events\ObatStokHabis;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Obat extends Model
{
    protected $table = 'obat';

    protected $fillable = [
        'kode_obat',
        'merk',
        'nama',
        'jenis',
        'kegunaan',
        'stok',
        'harga',
        'satuan'
    ];

    protected $casts = [
        'stok' => 'decimal:2',
    ];

    public function obatMasuk(): HasMany
    {
        return $this->hasMany(ObatMasuk::class);
    }

    public function kurangiStok($jumlah)
    {
        if (!is_numeric($jumlah) || $jumlah < 0) {
            throw new Exception("Jumlah harus berupa angka non-negatif");
        }

        $jumlah = round($jumlah, 2);

        if ($this->stok < $jumlah) {
            throw new Exception("Stok tidak mencukupi");
        }

        $this->stok -= $jumlah;
        $this->save();

        if ($this->stok == 0) {
            \Log::info('ObatStokHabis event triggered for obat: ' . $this->nama);
            event(new ObatStokHabis($this));
        }

        return true;
    }

    public function tambahStok($jumlah)
    {
        if (!is_numeric($jumlah) || $jumlah < 0) {
            throw new Exception("Jumlah harus berupa angka non-negatif");
        }

        $jumlah = round($jumlah, 2);  // Pembulatan ke 2 angka desimal

        $this->stok += $jumlah;
        $this->save();

        // Hapus item dari cache obat_kosong jika stok tidak lagi nol
        if ($this->stok > 0) {
            $obatKosong = Cache::get('obat_kosong', []);
            if (isset($obatKosong[$this->id])) {
                unset($obatKosong[$this->id]);
                Cache::put('obat_kosong', $obatKosong);
            }
        }

        return true;
    }
    public function formatStok()
    {
        $stok = $this->stok;
        $integerPart = floor($stok);
        $fractionalPart = $stok - $integerPart;

        $formattedStok = '';

        if ($integerPart > 0) {
            $formattedStok .= $integerPart;
        }

        if ($fractionalPart > 0) {
            if ($formattedStok !== '') {
                $formattedStok .= ' ';
            }

            if (abs($fractionalPart - 0.5) < 0.001) {
                $formattedStok .= "1/2";
            } elseif (abs($fractionalPart - 0.25) < 0.001) {
                $formattedStok .= "1/4";
            } elseif (abs($fractionalPart - 0.75) < 0.001) {
                $formattedStok .= "3/4";
            } else {
                // Untuk pecahan lainnya, gunakan nilai desimal
                $formattedStok = number_format($stok, 2);
            }
        }

        if ($formattedStok === '') {
            $formattedStok = '0';
        }

        return $formattedStok . " " . $this->satuan;
    }

    public function detailKunjungans()
    {
        return $this->hasMany(DetailKunjungan::class, 'id_obat');
    }
}