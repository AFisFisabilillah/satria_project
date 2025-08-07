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
        Schema::create('artikels', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string("foto");
            $table->LongText("isi");
            $table->date("tanggal");
            $table->string("kategori");
            $table->unsignedBigInteger("penulis");
            $table->foreign("penulis", "fk_penulis_super_admin")->references("id")->on("super_admins");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('artikels');
    }
};
