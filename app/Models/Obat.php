<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Exception;

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
        if (!is_numeric($jumlah) || $jumlah <= 0) {
            throw new Exception("Jumlah harus berupa angka positif");
        }

        $jumlah = round($jumlah, 2);  // Pembulatan ke 2 angka desimal

        if ($this->stok < $jumlah) {
            throw new Exception("Stok tidak mencukupi");
        }

        $this->stok -= $jumlah;
        $this->save();
        return true;
    }

    public function tambahStok($jumlah)
    {
        if (!is_numeric($jumlah) || $jumlah <= 0) {
            throw new Exception("Jumlah harus berupa angka positif");
        }

        $jumlah = round($jumlah, 2);  // Pembulatan ke 2 angka desimal

        $this->stok += $jumlah;
        $this->save();
        return true;
    }

    public function formatStok()
    {
        $stok = $this->stok;
        if ($stok == (int) $stok) {
            return (int) $stok . " " . $this->satuan;
        } elseif ($stok * 2 == (int) ($stok * 2)) {
            return ($stok * 2) . "/2 " . $this->satuan;
        } elseif ($stok * 4 == (int) ($stok * 4)) {
            return ($stok * 4) . "/4 " . $this->satuan;
        } else {
            return $stok . " " . $this->satuan;
        }
    }
}