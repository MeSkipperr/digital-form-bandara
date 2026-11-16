<?php

namespace Tests\Feature;

use App\Models\TemplateForm;
use App\Models\Tugas;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TugasControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_index_lists_the_tasks_of_the_form(): void
    {
        $form = TemplateForm::factory()->create(['nama' => 'Form Xray Atas']);
        Tugas::factory()->for($form, 'templateForm')->create(['nama' => 'Periksa kabel daya']);
        Tugas::factory()->for($form, 'templateForm')->create(['nama' => 'Cek konveyor']);
        Tugas::factory()->create(['nama' => 'Task form lain']);

        $this->get(route('admin.form.tugas.index', $form))
            ->assertOk()
            ->assertSeeText(['Inspeksi Alat / Form / Tugas', 'Kelola Tugas', 'Kembali ke Kelola Form', 'Form Xray Atas', 'Tambah Task'])
            ->assertSeeTextInOrder(['Periksa kabel daya', 'Cek konveyor'])
            ->assertSeeText(['Ubah Task', 'Hapus Task', 'Task Cek konveyor akan dihapus dari form ini.'])
            ->assertDontSeeText('Task form lain')
            ->assertSee('href="'.route('admin.form.index').'"', false);
    }

    public function test_index_shows_an_empty_state(): void
    {
        $this->get(route('admin.form.tugas.index', TemplateForm::factory()->create()))
            ->assertOk()
            ->assertSeeText('Belum ada task di form ini.');
    }

    public function test_store_adds_a_task_to_the_form(): void
    {
        $form = TemplateForm::factory()->create();

        $this->post(route('admin.form.tugas.store', $form), ['nama' => 'Kalibrasi detektor'])
            ->assertRedirect(route('admin.form.tugas.index', $form))
            ->assertSessionHas('status', 'Task berhasil ditambahkan.');

        $this->assertDatabaseHas('tugas', ['id_template_form' => $form->id, 'nama' => 'Kalibrasi detektor']);
    }

    public function test_store_requires_a_name(): void
    {
        $form = TemplateForm::factory()->create();

        $this->post(route('admin.form.tugas.store', $form), ['nama' => ''])
            ->assertSessionHasErrors(['nama' => 'Nama task wajib diisi.']);

        $this->assertDatabaseCount('tugas', 0);
    }

    public function test_update_renames_the_task(): void
    {
        $tugas = Tugas::factory()->create(['nama' => 'Cek konveyor']);

        $this->put(route('admin.form.tugas.update', [$tugas->templateForm, $tugas]), ['nama' => 'Cek konveyor dan sabuk'])
            ->assertRedirect(route('admin.form.tugas.index', $tugas->templateForm))
            ->assertSessionHas('status', 'Task berhasil diperbarui.');

        $this->assertSame('Cek konveyor dan sabuk', $tugas->refresh()->nama);
    }

    public function test_destroy_deletes_the_task(): void
    {
        $tugas = Tugas::factory()->create();

        $this->delete(route('admin.form.tugas.destroy', [$tugas->templateForm, $tugas]))
            ->assertRedirect(route('admin.form.tugas.index', $tugas->templateForm))
            ->assertSessionHas('status', 'Task berhasil dihapus.');

        $this->assertModelMissing($tugas);
    }

    public function test_tasks_of_another_form_are_not_found(): void
    {
        $tugas = Tugas::factory()->create();
        $formLain = TemplateForm::factory()->create();

        $this->put(route('admin.form.tugas.update', [$formLain, $tugas]), ['nama' => 'Diubah'])->assertNotFound();
        $this->delete(route('admin.form.tugas.destroy', [$formLain, $tugas]))->assertNotFound();

        $this->assertModelExists($tugas);
    }
}
