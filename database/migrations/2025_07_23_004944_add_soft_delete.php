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
        Schema::table('lowongans', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('status_histories', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('pendaftarans', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table("admin_cabangs", function (Blueprint $table) {
            $table->softDeletes();
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
