<?php

namespace App\Models;

use Database\Factories\FormAlatFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

#[Table(name: 'form_alat', timestamps: false)]
#[Fillable(['id_alat', 'id_template_form'])]
class FormAlat extends Model
{
    /** @use HasFactory<FormAlatFactory> */
    use HasFactory;

    /**
     * @return BelongsTo<Alat, $this>
     */
    public function alat(): BelongsTo
    {
        return $this->belongsTo(Alat::class, 'id_alat');
    }

    /**
     * @return BelongsTo<TemplateForm, $this>
     */
    public function templateForm(): BelongsTo
    {
        return $this->belongsTo(TemplateForm::class, 'id_template_form');
    }

    /**
     * @return HasMany<RiwayatPengecekan, $this>
     */
    public function riwayatPengecekan(): HasMany
    {
        return $this->hasMany(RiwayatPengecekan::class, 'id_form_alat');
    }

    /**
     * Whether an officer already filled (or skipped) an inspection of this form, which must be kept as history.
     */
    public function punyaRiwayatTercatat(): bool
    {
        return $this->riwayatPengecekan()->tercatat()->exists();
    }

    /**
     * Whether the alat and the template form of this row both still exist. Deleting either one nulls its column but
     * keeps the row, so the riwayat already recorded on it still shows the side that remains.
     */
    public function masihTerpasang(): bool
    {
        return $this->id_alat !== null && $this->id_template_form !== null;
    }

    /**
     * Rows whose alat and template form both still exist.
     *
     * @param  Builder<FormAlat>  $query
     */
    #[Scope]
    protected function terpasang(Builder $query): void
    {
        $query->whereNotNull('id_alat')->whereNotNull('id_template_form');
    }

    /**
     * Delete this form_alat with its pending (`tertunda`) riwayat, the empty rows generated every day.
     * Check punyaRiwayatTercatat() first: the riwayat that remain would lose both their alat and their form.
     */
    public function hapusBesertaRiwayatTertunda(): void
    {
        DB::transaction(function (): void {
            $this->riwayatPengecekan()->where('status', 'tertunda')->delete();
            $this->delete();
        });
    }

    /**
     * Before its alat or template form is deleted: drop the pending riwayat, and drop the row itself unless an officer
     * already recorded a riwayat on it. That row stays so the history keeps the side that is not deleted.
     */
    public function lepasSebelumIndukDihapus(): void
    {
        if ($this->punyaRiwayatTercatat()) {
            $this->riwayatPengecekan()->where('status', 'tertunda')->delete();

            return;
        }

        $this->hapusBesertaRiwayatTertunda();
    }
}
