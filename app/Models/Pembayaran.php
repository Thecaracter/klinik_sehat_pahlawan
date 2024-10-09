<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;
    protected $table = 'pembayaran';

    protected $fillable = [
        'kunjungan_id',
        'total_bayar',
        'metode_pembayaran',
    ];

    protected $casts = [
        'total_bayar' => 'decimal:2',
    ];

    public function kunjungan()
    {
        return $this->belongsTo(Kunjungan::class);
    }
}
