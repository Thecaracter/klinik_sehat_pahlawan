<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pasien extends Model
{
    use HasFactory;
    protected $table = 'pasien';

    protected $fillable = [
        'nik',
        'nama',
        'tanggal_lahir',
        'alamat',
        'no_hp'
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];
}
