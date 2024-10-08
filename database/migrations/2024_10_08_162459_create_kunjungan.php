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
        Schema::create('kunjungan', function (Blueprint $table) {
            $table->id();
            $table->string('pasien_nik');
            $table->unsignedBigInteger('user_id');
            $table->string('ditangani_oleh');
            $table->date('tanggal');
            $table->text('keluhan');
            $table->text('diagnosa')->nullable();
            $table->text('tindakan')->nullable();
            $table->string('status')->default('belum_selesai');
            $table->timestamps();

            $table->foreign('pasien_nik')->references('nik')->on('pasien')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kunjungan');
    }
};
