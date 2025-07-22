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
        Schema::table('pelamars', function (Blueprint $table) {
            $table->boolean("sudah_lengkap")->default(false);
            $table->string("kelamin_pelamar")->nullable();
        });

        Schema::table('lowongans', function (Blueprint $table) {
           $table->renameColumn("jumlah_lowongan", "kuota_lowongan");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pelamars', function (Blueprint $table) {
            $table->dropColumn("sudah_lengkap");
            $table->dropColumn("kelamin_pelamar");
        });

        Schema::table('lowongans', function (Blueprint $table) {
            $table->renameColumn("kuota_lowongan", "jumlah_lowongan");
        });
    }
};
