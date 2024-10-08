<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailKunjungan extends Model
{
    use HasFactory;
    protected $table = 'detail_kunjungan';

    protected $fillable = [
        'id_kunjungan',
        'id_obat',
        'jumlah_obat',
        'instruksi',
    ];
    protected $casts = [
        'jumlah_obat' => 'decimal:2',
    ];

    public function kunjungan()
    {
        return $this->belongsTo(Kunjungan::class, 'id_kunjungan');
    }

    public function obat()
    {
        return $this->belongsTo(Obat::class, 'id_obat');
    }
}
