<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use App\Models\Obat;

class ViewComposerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        View::composer('*', function ($view) {
            if (Cache::get('obat_kosong') === null) {
                $obatKosong = Obat::where('stok', 0)->get()->keyBy('id');
                Cache::put('obat_kosong', $obatKosong);
            }

            $obatKosong = Cache::get('obat_kosong');
            $view->with('obatKosong', $obatKosong);
        });
    }

    public function register()
    {
        //
    }
}