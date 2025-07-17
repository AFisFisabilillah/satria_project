<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cabang>
 */
class CabangFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "alamat_cabang" => $this->faker->address(),
            "no_telp" => $this->faker->phoneNumber(),
            "nama_cabang" => $this->faker->name(),
            "kota_cabang" => $this->faker->city(),
        ];
    }
}
