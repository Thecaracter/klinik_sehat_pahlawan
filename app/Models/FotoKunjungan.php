<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FotoKunjungan extends Model
{
    use HasFactory;

    protected $table = 'foto_kunjungan';

    protected $fillable = [
        'kunjungan_id',
        'nama',
        'foto',
    ];

    public function kunjungan()
    {
        return $this->belongsTo(Kunjungan::class);
    }
}
