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
            $table->string("type")->default("online");
            $table->unsignedBigInteger("admin_id")->nullable();
            $table->string("admin_type")->nullable();
            $table->index(["admin_id", "admin_type"]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pelamars', function (Blueprint $table) {
            $table->dropColumn("type");
            $table->dropColumn("admin_id");
            $table->dropColumn("admin_type");
        });
    }
};
