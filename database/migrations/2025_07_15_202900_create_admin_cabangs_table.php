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
        Schema::create('admin_cabangs', function (Blueprint $table) {
            $table->id();
            $table->string("nama_ac");
            $table->string("email_ac");
            $table->string("password_ac");
            $table->string("telp_ac");
            $table->unsignedBigInteger("cabang_id");
            $table->foreign("cabang_id")->references("id_cabang")->on("cabangs")->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_cabangs');
    }
};
