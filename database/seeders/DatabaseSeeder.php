<?php

namespace Database\Seeders;

use App\Models\SuperAdmin;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CabangSeed::class
        ]);

        SuperAdmin::create([
            "name_super_admin" => "admin",
            "email_super_admin" => "admin@gmail.com",
            "password_super_admin" => Hash::make("admin"),
        ]);
    }
}
