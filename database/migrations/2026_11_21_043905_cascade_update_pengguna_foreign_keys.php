<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The username can be changed from the Ubah Pengguna modal, so the riwayat and sesi of the pengguna follow the
     * new username. The delete rules stay as they are.
     */
    public function up(): void
    {
        $this->hapusForeignKey();

        Schema::table('riwayat_pengecekan', function (Blueprint $table) {
            $table->foreign('id_pengguna', 'fk_riwayat_pengguna')->references('id')->on('pengguna')->cascadeOnUpdate()->nullOnDelete();
        });

        Schema::table('sesi_pengguna', function (Blueprint $table) {
            $table->foreign('id_pengguna', 'fk_sesi_pengguna')->references('id')->on('pengguna')->cascadeOnUpdate()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->hapusForeignKey();

        Schema::table('riwayat_pengecekan', function (Blueprint $table) {
            $table->foreign('id_pengguna', 'fk_riwayat_pengguna')->references('id')->on('pengguna')->nullOnDelete();
        });

        Schema::table('sesi_pengguna', function (Blueprint $table) {
            $table->foreign('id_pengguna', 'fk_sesi_pengguna')->references('id')->on('pengguna')->cascadeOnDelete();
        });
    }

    /**
     * Drop the foreign keys this migration redefines.
     */
    protected function hapusForeignKey(): void
    {
        Schema::table('riwayat_pengecekan', function (Blueprint $table) {
            $table->dropForeign($this->foreignKey('fk_riwayat_pengguna', 'id_pengguna'));
        });

        Schema::table('sesi_pengguna', function (Blueprint $table) {
            $table->dropForeign($this->foreignKey('fk_sesi_pengguna', 'id_pengguna'));
        });
    }

    /**
     * SQLite cannot drop a foreign key by name, only by its columns.
     *
     * @return string|list<string>
     */
    protected function foreignKey(string $nama, string $kolom): string|array
    {
        return Schema::getConnection()->getDriverName() === 'sqlite' ? [$kolom] : $nama;
    }
};
