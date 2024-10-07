<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Obatmasuk extends Model
{
    use HasFactory;
    protected $table = 'obat_masuk';

    protected $fillable = [
        'obat_id',
        'nomor_batch',
        'jumlah',
        'harga_beli',
        'tanggal_kadaluarsa'
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'tanggal_kadaluarsa' => 'date',
    ];

    public function obat(): BelongsTo
    {
        return $this->belongsTo(Obat::class);
    }
}
