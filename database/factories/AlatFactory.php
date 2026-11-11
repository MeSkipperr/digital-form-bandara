<?php

namespace Database\Factories;

use App\Models\Alat;
use App\Models\KategoriAlat;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Alat>
 */
class AlatFactory extends Factory
{
    /**
     * Sample equipment grouped by ID prefix.
     *
     * @var array<string, array{model: list<string>, deskripsi: list<string>, lokasi: list<string>}>
     */
    protected const SAMPEL_ALAT = [
        'XRAY' => [
            'model' => ['Rapiscan 620XR', 'Smiths HiScan 10080'],
            'deskripsi' => ['Mesin X-Ray pemindai bagasi kabin', 'Mesin X-Ray untuk bagasi besar'],
            'lokasi' => ['Terminal A - Check-in 1', 'Terminal A - Check-in 2', 'Terminal B - Area Kargo'],
        ],
        'BOMB' => [
            'model' => ['Robot EOD Talon', 'Trace Detector ETD-3'],
            'deskripsi' => ['Robot penjinak bom untuk penanganan paket mencurigakan', 'Pendeteksi jejak bahan peledak'],
            'lokasi' => ['Gudang Keamanan Utama', 'Pos Pemeriksaan Gerbang C'],
        ],
        'METL' => [
            'model' => ['Garrett PD 6500i', 'Handheld Garrett SuperScanner V'],
            'deskripsi' => ['Walk-through metal detector', 'Metal detector genggam untuk pemeriksaan tubuh'],
            'lokasi' => ['Gerbang Keberangkatan 1', 'Gerbang Keberangkatan 2', 'Gerbang Kedatangan 1'],
        ],
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $prefix = fake()->randomElement(array_keys(self::SAMPEL_ALAT));
        $sampel = self::SAMPEL_ALAT[$prefix];

        return [
            'id' => fake()->unique()->numerify($prefix.'-###'),
            'id_kategori_alat' => KategoriAlat::factory(),
            'status' => fake()->randomElement(['bagus', 'perlu_perbaikan']),
            'model' => fake()->randomElement($sampel['model']),
            'deskripsi' => fake()->randomElement($sampel['deskripsi']),
            'lokasi' => fake()->randomElement($sampel['lokasi']),
        ];
    }

    /**
     * Indicate that the equipment is in good condition.
     */
    public function bagus(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'bagus',
        ]);
    }

    /**
     * Indicate that the equipment needs repair.
     */
    public function perluPerbaikan(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'perlu_perbaikan',
        ]);
    }
}
