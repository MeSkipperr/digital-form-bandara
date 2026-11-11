<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AdminPagesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    /**
     * @return array<string, array{0: string, 1: list<string>}>
     */
    public static function halamanAdmin(): array
    {
        return [
            'dasbor' => ['/admin/dasbor', ['Dasbor', 'Perbandingan Status Alat', 'Jumlah Form yang Diisi Setiap Hari', 'Form Hari Ini', 'Pengguna Aktif']],
            'riwayat' => ['/admin/riwayat', ['Riwayat Pengecekan', 'Sensor samping tidak merespons', 'Detail Pengecekan', 'Buka Detail Alat']],
        ];
    }

    /**
     * @param  list<string>  $teksYangTampil
     */
    #[DataProvider('halamanAdmin')]
    public function test_admin_page_renders_the_design_content(string $url, array $teksYangTampil): void
    {
        $this->get($url)
            ->assertOk()
            ->assertSeeText($teksYangTampil, escape: false);
    }
}
