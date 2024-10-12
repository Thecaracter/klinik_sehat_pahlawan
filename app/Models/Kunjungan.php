<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kunjungan extends Model
{
    use HasFactory;

    protected $table = 'kunjungan';

    protected $fillable = [
        'pasien_id',
        'user_id',
        'ditangani_oleh',
        'tanggal',
        'keluhan',
        'pemerikasaan_awal',
        'diagnosa',
        'tindakan',
        'status',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function pasien()
    {
        return $this->belongsTo(Pasien::class, 'pasien_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function fotoKunjungan()
    {
        return $this->hasMany(FotoKunjungan::class);
    }
    public function detailKunjungans()
    {
        return $this->hasMany(DetailKunjungan::class, 'id_kunjungan');
    }
    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class);
    }
}
