<?php

namespace App\Models;

use Database\Factories\PenggunaFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Table(name: 'pengguna', keyType: 'string', incrementing: false, timestamps: false)]
#[Fillable(['id', 'nama', 'peran', 'kata_sandi_hash', 'aktif'])]
#[Hidden(['kata_sandi_hash'])]
class Pengguna extends Model
{
    /** @use HasFactory<PenggunaFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'kata_sandi_hash' => 'hashed',
            'aktif' => 'boolean',
            'dibuat_pada' => 'datetime',
        ];
    }

    /**
     * @return HasMany<RiwayatPengecekan, $this>
     */
    public function riwayatPengecekan(): HasMany
    {
        return $this->hasMany(RiwayatPengecekan::class, 'id_pengguna');
    }

    /**
     * @return HasMany<SesiPengguna, $this>
     */
    public function sesiPengguna(): HasMany
    {
        return $this->hasMany(SesiPengguna::class, 'id_pengguna');
    }
}
