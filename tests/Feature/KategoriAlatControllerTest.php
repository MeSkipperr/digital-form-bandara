<?php

namespace Tests\Feature;

use App\Models\Alat;
use App\Models\KategoriAlat;
use App\Models\TemplateForm;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KategoriAlatControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_creates_kategori_and_returns_it_as_json(): void
    {
        $this->postJson(route('admin.kategori-alat.store'), ['nama' => 'ETD'])
            ->assertCreated()
            ->assertJson(['nama' => 'ETD']);

        $this->assertDatabaseHas('kategori_alat', ['nama' => 'ETD']);
    }

    public function test_store_rejects_empty_and_duplicate_names(): void
    {
        KategoriAlat::factory()->create(['nama' => 'X-RAY']);

        $this->postJson(route('admin.kategori-alat.store'), ['nama' => ''])
            ->assertUnprocessable()->assertJsonValidationErrors(['nama' => 'Nama kategori wajib diisi.']);
        $this->postJson(route('admin.kategori-alat.store'), ['nama' => 'X-RAY'])
            ->assertUnprocessable()->assertJsonValidationErrors(['nama' => 'Kategori dengan nama ini sudah ada.']);
    }

    public function test_update_renames_kategori_and_allows_keeping_its_own_name(): void
    {
        $kategori = KategoriAlat::factory()->create(['nama' => 'ETD']);

        $this->putJson(route('admin.kategori-alat.update', $kategori), ['nama' => 'ETD'])->assertOk();
        $this->putJson(route('admin.kategori-alat.update', $kategori), ['nama' => 'Explosive Trace'])
            ->assertOk()
            ->assertJson(['id' => $kategori->id, 'nama' => 'Explosive Trace']);

        $this->assertDatabaseHas('kategori_alat', ['id' => $kategori->id, 'nama' => 'Explosive Trace']);
    }

    public function test_destroy_unused_kategori_needs_no_target(): void
    {
        $kategori = KategoriAlat::factory()->create();

        $this->deleteJson(route('admin.kategori-alat.destroy', $kategori))->assertOk();

        $this->assertModelMissing($kategori);
    }

    public function test_destroy_moves_alat_and_forms_to_the_target_kategori(): void
    {
        $hnmd = KategoriAlat::factory()->create(['nama' => 'HNMD']);
        $xray = KategoriAlat::factory()->create(['nama' => 'X-RAY']);
        $alat = Alat::factory()->for($hnmd, 'kategoriAlat')->create();
        $templateForm = TemplateForm::factory()->for($hnmd, 'kategoriAlat')->create();

        $this->deleteJson(route('admin.kategori-alat.destroy', $hnmd), ['pindah_ke' => $xray->id])
            ->assertOk()
            ->assertJson(['id' => $hnmd->id, 'pindah_ke' => $xray->id]);

        $this->assertModelMissing($hnmd);
        $this->assertSame($xray->id, $alat->fresh()->id_kategori_alat);
        $this->assertSame($xray->id, $templateForm->fresh()->id_kategori_alat);
    }

    public function test_destroy_used_kategori_requires_a_different_target(): void
    {
        $kategori = KategoriAlat::factory()->create();
        Alat::factory()->for($kategori, 'kategoriAlat')->create();

        $this->deleteJson(route('admin.kategori-alat.destroy', $kategori))
            ->assertUnprocessable()->assertJsonValidationErrors('pindah_ke');
        $this->deleteJson(route('admin.kategori-alat.destroy', $kategori), ['pindah_ke' => $kategori->id])
            ->assertUnprocessable()->assertJsonValidationErrors('pindah_ke');

        $this->assertModelExists($kategori);
    }
}
