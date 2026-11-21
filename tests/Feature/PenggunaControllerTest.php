<?php

namespace Tests\Feature;

use App\Models\Pengguna;
use App\Models\RiwayatPengecekan;
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
            ->assertSeeText(['Pengguna', 'Tambah Pengguna', 'Yoga Pratama', '@yoga001', 'Status Akun', 'Nonaktif', 'Menampilkan 2 dari 2 pengguna', 'Ubah Pengguna', 'Nama Lengkap', 'Simpan Pengguna'])
            ->assertDontSeeText('Email');
    }

    public function test_index_paginates_and_searches(): void
    {
        Pengguna::factory()->count(27)->create();
        Pengguna::factory()->create(['id' => 'cari001', 'nama' => 'Zzz Target']);

        $this->get(route('admin.pengguna.index'))->assertSeeText('Menampilkan 25 dari 28 pengguna');
        $this->get(route('admin.pengguna.index', ['halaman' => 2]))->assertSeeText('Menampilkan 3 dari 28 pengguna');
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

    public function test_store_can_create_an_inactive_pengguna(): void
    {
        $this->post(route('admin.pengguna.store'), [
            'id' => 'nonaktif001',
            'nama' => 'Pengguna Nonaktif',
            'peran' => 'petugas',
            'kata_sandi' => 'rahasia123',
            'aktif' => '0',
        ])->assertRedirect();

        $this->assertFalse(Pengguna::find('nonaktif001')->aktif);
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

        $this->put(route('admin.pengguna.update', $pengguna), ['id' => 'yoga001', 'nama' => 'Nama Baru', 'peran' => 'admin', 'aktif' => '0', 'kata_sandi' => ''])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $pengguna->refresh();
        $this->assertSame('Nama Baru', $pengguna->nama);
        $this->assertSame('admin', $pengguna->peran);
        $this->assertFalse($pengguna->aktif);
        $this->assertSame($hashLama, $pengguna->kata_sandi_hash);

        $this->put(route('admin.pengguna.update', $pengguna), ['id' => 'yoga001', 'nama' => 'Nama Baru', 'peran' => 'admin', 'aktif' => '1', 'kata_sandi' => 'sandibaru123'])
            ->assertRedirect();

        $this->assertTrue(Hash::check('sandibaru123', $pengguna->fresh()->kata_sandi_hash));
    }

    public function test_update_changes_the_username_and_moves_the_riwayat_to_it(): void
    {
        $pengguna = Pengguna::factory()->create(['id' => 'yoga001']);
        $riwayat = RiwayatPengecekan::factory()->for($pengguna)->bagus()->create();

        $this->put(route('admin.pengguna.update', $pengguna), ['id' => 'yoga999', 'nama' => $pengguna->nama, 'peran' => $pengguna->peran, 'aktif' => '1'])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertDatabaseMissing('pengguna', ['id' => 'yoga001']);
        $this->assertDatabaseHas('pengguna', ['id' => 'yoga999']);
        $this->assertDatabaseHas('riwayat_pengecekan', ['id' => $riwayat->id, 'id_pengguna' => 'yoga999']);
    }

    public function test_update_rejects_a_username_used_by_another_pengguna_and_reopens_its_modal(): void
    {
        $pengguna = Pengguna::factory()->create(['id' => 'yoga001', 'nama' => 'Nama Lama']);
        Pengguna::factory()->create(['id' => 'ada001']);

        $this->from(route('admin.pengguna.index'))
            ->followingRedirects()
            ->put(route('admin.pengguna.update', $pengguna), ['_form' => 'ubah-pengguna-yoga001', 'id' => 'ada001', 'nama' => 'Nama Diketik', 'peran' => 'petugas', 'aktif' => '1'])
            ->assertOk()
            ->assertSee('data-buka-ulang', false)
            ->assertSee('value="ada001"', false)
            ->assertSeeText('Username ini sudah dipakai.')
            ->assertDontSee('role="alert"', false);

        $this->assertDatabaseHas('pengguna', ['id' => 'yoga001', 'nama' => 'Nama Lama']);
    }

    public function test_renaming_the_logged_in_pengguna_keeps_them_logged_in(): void
    {
        $admin = Pengguna::factory()->create(['id' => 'admin001', 'peran' => 'admin']);

        $this->actingAs($admin)
            ->put(route('admin.pengguna.update', $admin), ['id' => 'admin002', 'nama' => $admin->nama, 'peran' => 'admin', 'aktif' => '1'])
            ->assertRedirect();

        $this->assertAuthenticatedAs(Pengguna::find('admin002'));
    }

    public function test_store_hashes_the_password_with_the_hash_password_key(): void
    {
        config(['hashing.password_key' => 'kunci-uji']);

        $this->post(route('admin.pengguna.store'), [
            'id' => 'baru001',
            'nama' => 'Pengguna Baru',
            'peran' => 'petugas',
            'kata_sandi' => 'rahasia123',
        ])->assertRedirect();

        $pengguna = Pengguna::find('baru001');
        $this->assertTrue($pengguna->cocokKataSandi('rahasia123'));
        $this->assertFalse($pengguna->cocokKataSandi('salah1234'));
        $this->assertTrue(Hash::check(hash_hmac('sha256', 'rahasia123', 'kunci-uji'), $pengguna->kata_sandi_hash));
        $this->assertFalse(Hash::check('rahasia123', $pengguna->kata_sandi_hash));
    }

    public function test_update_hashes_the_new_password_with_the_hash_password_key(): void
    {
        config(['hashing.password_key' => 'kunci-uji']);
        $pengguna = Pengguna::factory()->create();

        $this->put(route('admin.pengguna.update', $pengguna), ['id' => $pengguna->id, 'nama' => 'Nama Baru', 'peran' => 'petugas', 'aktif' => '1', 'kata_sandi' => 'sandibaru123'])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertTrue($pengguna->fresh()->cocokKataSandi('sandibaru123'));
    }

    public function test_store_rejects_a_username_that_cannot_be_used_in_urls(): void
    {
        $this->post(route('admin.pengguna.store'), ['id' => 'yoga 001', 'nama' => 'Yoga', 'peran' => 'petugas', 'kata_sandi' => 'rahasia123'])
            ->assertSessionHasErrors(['id' => 'Username hanya boleh berisi huruf, angka, tanda - dan _.']);

        $this->assertDatabaseCount('pengguna', 0);
    }

    public function test_failed_store_keeps_the_input_but_never_flashes_the_password(): void
    {
        $this->post(route('admin.pengguna.store'), ['_form' => 'tambah-pengguna', 'id' => 'baru001', 'nama' => 'Nama Diketik', 'peran' => 'petugas', 'kata_sandi' => 'pendek'])
            ->assertSessionHasErrors(['kata_sandi' => 'Kata sandi minimal 8 karakter.'])
            ->assertSessionHasInput('nama', 'Nama Diketik')
            ->assertSessionMissing('_old_input.kata_sandi');
    }

    public function test_failed_store_reopens_the_tambah_modal_with_the_old_input(): void
    {
        Pengguna::factory()->create(['id' => 'ada001']);

        $this->from(route('admin.pengguna.index'))
            ->followingRedirects()
            ->post(route('admin.pengguna.store'), ['_form' => 'tambah-pengguna', 'id' => 'ada001', 'nama' => 'Nama Diketik', 'peran' => 'petugas', 'kata_sandi' => 'rahasia123'])
            ->assertOk()
            ->assertSee('data-buka-ulang', false)
            ->assertSee('value="Nama Diketik"', false)
            ->assertSeeText('Username ini sudah dipakai.');
    }

    public function test_tambah_modal_posts_a_new_pengguna_even_when_the_table_has_rows(): void
    {
        Pengguna::factory()->create(['id' => 'yoga001']);

        $this->get(route('admin.pengguna.index'))
            ->assertOk()
            ->assertSee('action="'.route('admin.pengguna.store').'"', false)
            ->assertSee('name="_form" value="tambah-pengguna"', false);
    }
}
