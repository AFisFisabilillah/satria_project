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
        Schema::table('pendaftarans', function (Blueprint $table) {
            $table->dropForeign('pendaftarans_pelamar_id_foreign');
            $table->dropUnique("pendaftarans_pelamar_id_unique");
            $table->foreign("pelamar_id")->references("id_pelamar")->on("pelamars");
            $table->unique(["lowongan_id", "pelamar_id"], "pelamar_lowongan_unique");
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
