<?php

namespace Database\Factories;

use App\Models\KategoriAlat;
use App\Models\TemplateForm;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TemplateForm>
 */
class TemplateFormFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_kategori_alat' => KategoriAlat::factory(),
            'path_gambar' => fake()->optional()->randomElement([
                'template/xray.png',
                'template/pendeteksi-bom.png',
                'template/metal-detector.png',
            ]),
            'nama' => fake()->randomElement([
                'Form Pengecekan X-Ray',
                'Form Pengecekan Pendeteksi Bom',
                'Form Pengecekan Metal Detector',
            ]),
        ];
    }
}
