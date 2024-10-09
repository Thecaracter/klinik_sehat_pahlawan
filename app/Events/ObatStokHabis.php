<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Obat;

class ObatStokHabis
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $obat;

    public function __construct(Obat $obat)
    {
        $this->obat = $obat;
    }
}