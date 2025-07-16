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
        Schema::create('pendaftarans', function (Blueprint $table) {
            $table->id("id_pendaftaran");
            $table->unsignedBigInteger("pelamar_id");
            $table->unsignedBigInteger("lowongan_id");
            $table->foreign("pelamar_id")->references("id_pelamar")->on("pelamars");
            $table->foreign("lowongan_id")->references("id_lowongan")->on("lowongans");
            $table->timestamp("waktu_pendaftaran");
            $table->string("status_pendaftaran")->default("diproses");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pendaftarans');
    }
};
