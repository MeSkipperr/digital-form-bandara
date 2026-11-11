<?php

namespace Database\Factories;

use App\Models\KategoriAlat;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<KategoriAlat>
 */
class KategoriAlatFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama' => fake()->randomElement(['X-Ray', 'Pendeteksi Bom', 'Metal Detector']),
        ];
    }
}
