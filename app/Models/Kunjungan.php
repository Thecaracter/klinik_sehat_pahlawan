<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kunjungan extends Model
{
    use HasFactory;

    protected $table = 'kunjungan';

    protected $fillable = [
        'pasien_nik',
        'user_id',
        'ditangani_oleh',
        'tanggal',
        'keluhan',
        'diagnosa',
        'tindakan',
        'status',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function pasien()
    {
        return $this->belongsTo(Pasien::class, 'pasien_nik', 'nik');
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
