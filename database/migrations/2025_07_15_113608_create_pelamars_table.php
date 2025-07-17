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
        Schema::create('pelamars', function (Blueprint $table) {
            $table->id("id_pelamar");
            $table->string("nama_pelamar", 100);
            $table->string("email_pelamar", 100)->unique("email_unique");
            $table->string("telp_pelamar", 100)->unique("telp_unique");
            $table->string("ttl_pelamar", 100)->nullable();
            $table->string("domisili_pelamar", 100);
            $table->string("status_nikah_pelamar", 100)->nullable();
            $table->string("profile_pelamar", 100)->nullable();
            $table->string("cv_pelamar", 100)->nullable();
            $table->string("password_pelamar");
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pelamars');
    }
};
