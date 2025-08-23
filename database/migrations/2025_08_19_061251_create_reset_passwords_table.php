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
        Schema::create('reset_passwords', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("pelamar_id");
            $table->foreign("pelamar_id")->references("id_pelamar")->on("pelamars");
            $table->string("verification_code");
            $table->timestamp("expired_code");
            $table->string("token")->unique()->nullable();
            $table->boolean("is_valid");
            $table->boolean("is_used");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reset_passwords');
    }
};
