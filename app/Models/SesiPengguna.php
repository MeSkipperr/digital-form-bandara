<?php

namespace App\Models;

use Database\Factories\SesiPenggunaFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Table(name: 'sesi_pengguna', key: 'token', keyType: 'string', incrementing: false, timestamps: false)]
#[Fillable(['token', 'id_pengguna', 'aktif_sampai'])]
#[Hidden(['token'])]
class SesiPengguna extends Model
{
    /** @use HasFactory<SesiPenggunaFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'aktif_sampai' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Pengguna, $this>
     */
    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna');
    }
}
