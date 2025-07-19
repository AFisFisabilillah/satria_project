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
        Schema::table("lowongans", function (Blueprint $table) {
           $table->dropColumn("jam_kerja");
           $table->string("currency");
           $table->string("status_lowongan");
           $table->bigInteger("jumlah_lowongan");
           $table->fullText(["nama_lowongan","deskripsi_lowongan","posisi_lowongan"], "full_text_search_lowongan");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
