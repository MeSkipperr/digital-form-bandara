<?php

namespace Database\Factories;

use App\Models\Pengguna;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<Pengguna>
 */
class PenggunaFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $kataSandi;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $nama = fake('id_ID')->firstName().' '.fake('id_ID')->lastName();
        $prefix = Str::upper(Str::substr(Str::before($nama, ' '), 0, 4));

        return [
            'id' => fake()->unique()->numerify($prefix.'-###'),
            'nama' => $nama,
            'peran' => 'petugas',
            'kata_sandi_hash' => static::$kataSandi ??= Hash::make('password'),
            'aktif' => true,
        ];
    }

    /**
     * Indicate that the user is an admin.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'peran' => 'admin',
        ]);
    }

    /**
     * Indicate that the user is an officer.
     */
    public function petugas(): static
    {
        return $this->state(fn (array $attributes) => [
            'peran' => 'petugas',
        ]);
    }

    /**
     * Indicate that the user is inactive.
     */
    public function nonaktif(): static
    {
        return $this->state(fn (array $attributes) => [
            'aktif' => false,
        ]);
    }
}
