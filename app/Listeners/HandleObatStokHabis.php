<?php

namespace App\Listeners;

use App\Events\ObatStokHabis;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandleObatStokHabis
{
    public function handle(ObatStokHabis $event)
    {
        Log::info('HandleObatStokHabis executed for obat: ' . $event->obat->nama);

        $obatKosong = Cache::get('obat_kosong', []);
        $obatKosong[$event->obat->id] = $event->obat;
        Cache::put('obat_kosong', $obatKosong);

        Log::info('obat_kosong cache updated: ' . json_encode(Cache::get('obat_kosong')));
    }
}