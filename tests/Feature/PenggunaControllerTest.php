<?php

namespace Tests\Feature;

use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PenggunaControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_index_lists_pengguna_with_the_design_content(): void
    {
        Pengguna::factory()->create(['id' => 'yoga001', 'nama' => 'Yoga Pratama']);
        Pengguna::factory()->nonaktif()->create(['id' => 'putu004', 'nama' => 'Putu Arya']);

        $this->get(route('admin.pengguna.index'))
            ->assertOk()
            ->assertSeeText(['Kelola Pengguna', 'Tambah Pengguna', 'Yoga Pratama', '@yoga001', 'Nonaktif', 'Menampilkan 2 dari 2 pengguna', 'Ubah Pengguna', 'Ubah Kata Sandi']);
    }

    public function test_index_paginates_and_searches(): void
    {
        Pengguna::factory()->count(8)->create();
        Pengguna::factory()->create(['id' => 'cari001', 'nama' => 'Zzz Target']);

        $this->get(route('admin.pengguna.index'))->assertSeeText('Menampilkan 6 dari 9 pengguna');
        $this->get(route('admin.pengguna.index', ['halaman' => 2]))->assertSeeText('Menampilkan 3 dari 9 pengguna');
        $this->get(route('admin.pengguna.index', ['cari' => 'cari001']))
            ->assertSeeText('Zzz Target')
            ->assertSeeText('Menampilkan 1 dari 1 pengguna');
        $this->get(route('admin.pengguna.index', ['cari' => 'tidak-ada']))->assertSee('Tidak ada pengguna yang cocok.');
    }

    public function test_store_creates_hashed_pengguna(): void
    {
        $this->post(route('admin.pengguna.store'), [
            'id' => 'baru001',
            'nama' => 'Pengguna Baru',
            'peran' => 'admin',
            'kata_sandi' => 'rahasia123',
        ])->assertRedirect();

        $pengguna = Pengguna::find('baru001');
        $this->assertSame('admin', $pengguna->peran);
        $this->assertTrue($pengguna->aktif);
        $this->assertTrue(Hash::check('rahasia123', $pengguna->kata_sandi_hash));
    }

    public function test_store_validates_input(): void
    {
        Pengguna::factory()->create(['id' => 'ada001']);

        $this->post(route('admin.pengguna.store'), ['id' => 'ada001', 'peran' => 'supervisor', 'kata_sandi' => 'pendek'])
            ->assertSessionHasErrors(['id', 'nama', 'peran', 'kata_sandi']);
    }

    public function test_update_changes_fields_and_optionally_the_password(): void
    {
        $pengguna = Pengguna::factory()->create(['id' => 'yoga001']);
        $hashLama = $pengguna->kata_sandi_hash;

        $this->put(route('admin.pengguna.update', $pengguna), ['nama' => 'Nama Baru', 'peran' => 'admin', 'aktif' => '0', 'kata_sandi' => ''])
            ->assertRedirect();

        $pengguna->refresh();
        $this->assertSame('Nama Baru', $pengguna->nama);
        $this->assertSame('admin', $pengguna->peran);
        $this->assertFalse($pengguna->aktif);
        $this->assertSame($hashLama, $pengguna->kata_sandi_hash);

        $this->put(route('admin.pengguna.update', $pengguna), ['nama' => 'Nama Baru', 'peran' => 'admin', 'aktif' => '1', 'kata_sandi' => 'sandibaru123'])
            ->assertRedirect();

        $this->assertTrue(Hash::check('sandibaru123', $pengguna->fresh()->kata_sandi_hash));
    }
}
