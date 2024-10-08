<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('detail_kunjungan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_kunjungan');
            $table->unsignedBigInteger('id_obat');
            $table->decimal('jumlah_obat', 8, 2);
            $table->text('instruksi');
            $table->timestamps();

            $table->foreign('id_kunjungan')->references('id')->on('kunjungan')->onDelete('cascade');
            $table->foreign('id_obat')->references('id')->on('obat')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_kunjungan');
    }
};
