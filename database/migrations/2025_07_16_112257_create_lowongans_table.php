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
            $table->unsignedBigInteger("admin_cabang_id");
            $table->foreign('admin_cabang_id')->references('id')->on('admin_cabangs');
            $table->string("nama_lowongan");
            $table->text("deskripsi_lowongan");
            $table->text("syarat_lowongan");
            $table->string("negara_lowongan");
            $table->string("posisi_lowongan");
            $table->string("jam_kerja");
            $table->bigInteger("gaji_lowongan");
            $table->timestamp("deadline_lowongan");
            $table->string("kontrak_lowongan");
            $table->string("lokasi_lowongan");
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
