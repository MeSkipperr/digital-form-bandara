<?php

namespace Database\Factories;

use App\Models\FormAlat;
use App\Models\Pengguna;
use App\Models\RiwayatPengecekan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RiwayatPengecekan>
 */
class RiwayatPengecekanFactory extends Factory
{
    /**
     * Sample notes grouped by inspection status.
     *
     * @var array<string, list<string>>
     */
    protected const CATATAN_PER_STATUS = [
        'bagus' => [
            'Semua fungsi normal, citra jelas',
            'Sensitivitas normal dengan benda uji',
        ],
        'kurang_bagus' => [
            'Sensor sering error saat uji objek',
            'Alarm berbunyi tanpa objek logam',
        ],
        'tertunda' => [
            'Baterai robot perlu diisi ulang sebelum pengecekan',
        ],
        'terlewati' => [
            'Kalibrasi belum dilakukan, pengecekan dilewati',
        ],
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = fake()->randomElement(array_keys(self::CATATAN_PER_STATUS));

        return [
            'id_form_alat' => FormAlat::factory(),
            'id_pengguna' => Pengguna::factory(),
            'catatan' => fake()->randomElement(self::CATATAN_PER_STATUS[$status]),
            'status' => $status,
            'dicek_pada' => fake()->dateTimeBetween('-2 weeks'),
        ];
    }

    /**
     * Set the inspection status along with a matching note.
     */
    public function status(string $status): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => $status,
            'catatan' => fake()->randomElement(self::CATATAN_PER_STATUS[$status]),
        ]);
    }

    public function bagus(): static
    {
        return $this->status('bagus');
    }

    public function kurangBagus(): static
    {
        return $this->status('kurang_bagus');
    }

    /**
     * A pending inspection that nobody has checked yet.
     */
    public function tertunda(): static
    {
        return $this->state(fn (array $attributes) => [
            'id_pengguna' => null,
            'catatan' => null,
            'status' => 'tertunda',
            'dicek_pada' => null,
        ]);
    }

    public function terlewati(): static
    {
        return $this->status('terlewati');
    }
}
