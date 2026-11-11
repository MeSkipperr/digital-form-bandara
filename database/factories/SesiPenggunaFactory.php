<?php

namespace Database\Factories;

use App\Models\Pengguna;
use App\Models\SesiPengguna;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SesiPengguna>
 */
class SesiPenggunaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'token' => bin2hex(random_bytes(32)),
            'id_pengguna' => Pengguna::factory(),
            'aktif_sampai' => fake()->dateTimeBetween('+1 hour', '+1 day'),
        ];
    }

    /**
     * Indicate that the session has expired.
     */
    public function kedaluwarsa(): static
    {
        return $this->state(fn (array $attributes) => [
            'aktif_sampai' => fake()->dateTimeBetween('-1 week', '-1 hour'),
        ]);
    }
}
