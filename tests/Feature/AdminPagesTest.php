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
            'pengguna' => ['/admin/pengguna', ['Kelola Pengguna', 'Tambah Pengguna', 'Menampilkan 6 dari 24 pengguna', 'Ubah Pengguna', 'Ubah Kata Sandi']],
            'alat' => ['/admin/alat', ['Kelola Alat', 'XRAY-01', 'Gudang Belakang', 'Detail', 'Hapus', 'Tambah Alat', 'Ubah Alat', 'Semua kategori', 'Semua status', 'Perlu Perbaikan', 'Metal Detector', 'Tambah Kategori', 'Nama Kategori']],
            'detail alat' => ['/admin/alat/XRAY-01', ['Detail Alat', 'Informasi Alat', 'Kategori', 'X-Ray', 'Bagus', 'Dibuat pada', 'Ubah Alat', 'Unduh / Cetak QR', 'Form Pengecekan', '3 form terpasang pada alat ini']],
            'kelola form' => ['/admin/alat/XRAY-01/form', ['Kelola Form', 'Pilih Form', 'XRAY-01, Xray Pro Max', 'Tambah Form', 'Tarik gambar ke sini atau pilih file']],
            'kelola task' => ['/admin/alat/XRAY-01/form/1', ['Kelola Task', 'Form Xray Atas', 'XRAY-01, 5 task', 'Periksa kabel daya', 'Tambah Task']],
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

    public function test_status_filter_only_lists_matching_alat(): void
    {
        $this->get('/admin/alat?status=perlu_perbaikan')
            ->assertOk()
            ->assertSee('Lobi Utama')
            ->assertSee('Terminal Kargo')
            ->assertDontSee('Pintu Masuk A');
    }

    public function test_kategori_filter_only_lists_matching_alat(): void
    {
        $this->get('/admin/alat?kategori=3')
            ->assertOk()
            ->assertSee('METL-01')
            ->assertDontSee('XRAY-01')
            ->assertDontSee('BOMB-01');
    }

    public function test_search_matches_id_model_and_lokasi(): void
    {
        $this->get('/admin/alat?cari=itemiser')->assertOk()->assertSee('BOMB-01')->assertDontSee('XRAY-01');
        $this->get('/admin/alat?cari=pintu masuk')->assertOk()->assertSee('XRAY-03')->assertDontSee('XRAY-01');
        $this->get('/admin/alat?cari=metl-01')->assertOk()->assertSee('Ruang Transit')->assertDontSee('XRAY-01');
    }

    public function test_search_kategori_and_status_filters_combine(): void
    {
        $this->get('/admin/alat?cari=xray&kategori=1&status=perlu_perbaikan')
            ->assertOk()
            ->assertSee('XRAY-02')
            ->assertDontSee('XRAY-01')
            ->assertDontSee('XRAY-03')
            ->assertDontSee('BOMB-01');
    }

    public function test_alat_list_shows_empty_state_when_no_alat_matches(): void
    {
        $this->get('/admin/alat?cari=tidak-ada')
            ->assertOk()
            ->assertSee('Tidak ada alat yang cocok dengan filter.');
    }

    public function test_status_cannot_be_managed_from_kelola_alat(): void
    {
        $this->get('/admin/alat')
            ->assertOk()
            ->assertDontSee('Kelola Status')
            ->assertDontSee('Tambah Status')
            ->assertDontSee('type="radio" name="status"', false);
    }

    public function test_alat_form_offers_existing_kategori_and_the_add_kategori_popup(): void
    {
        $this->get('/admin/alat')
            ->assertOk()
            ->assertSee('id="modal-tambah-kategori"', false)
            ->assertSee('data-modal-open="modal-tambah-kategori"', false)
            ->assertSee('name="id_kategori_alat" value="1"', false)
            ->assertSee('name="id_kategori_alat" value="3"', false);
    }

    public function test_unknown_alat_returns_not_found(): void
    {
        $this->get('/admin/alat/XRAY-99')->assertNotFound();
        $this->get('/admin/alat/XRAY-01/form/99')->assertNotFound();
    }
}
