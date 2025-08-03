<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::table("super_admins", function (Blueprint $table) {
            $table->string('photo_profile')->nullable()->after('email_super_admin');
        });

        Schema::table("admin_cabangs", function (Blueprint $table) {
            $table->string('photo_profile')->nullable()->after('email_ac');
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
