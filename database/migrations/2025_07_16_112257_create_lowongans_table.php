<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('lowongans', function (Blueprint $table) {
            $table->id("id_lowongan");
            $table->string("nama_lowongan");
            $table->text("deskripsi_lowongan");
            $table->text("syarat_lowongan");
            $table->string("negara_lowongan");
            $table->string("posisi_lowongan");
            $table->string("jam_kerja");
            $table->string("max_gaji_lowongan");
            $table->bigInteger("min_gaji_lowongan");
            $table->string("sip2mi");
            $table->date("batas_waktu");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lowongans');
    }
};
