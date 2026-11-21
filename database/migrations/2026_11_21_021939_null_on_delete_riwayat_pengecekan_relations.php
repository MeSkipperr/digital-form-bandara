<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Deleting an alat, template form or pengguna keeps its riwayat pengecekan as history: the relation becomes null
     * instead of being refused. form_alat keeps the surviving side, so a riwayat still shows its form after the alat is
     * deleted and vice versa. Sessions of a deleted pengguna are removed with it. Kategori stays restricted.
     */
    public function up(): void
    {
        $this->hapusForeignKey();

        Schema::table('form_alat', function (Blueprint $table) {
            $table->string('id_alat', 20)->nullable()->change();
            $table->unsignedBigInteger('id_template_form')->nullable()->change();
        });

        Schema::table('riwayat_pengecekan', function (Blueprint $table) {
            $table->unsignedBigInteger('id_form_alat')->nullable()->change();
        });

        Schema::table('form_alat', function (Blueprint $table) {
            $table->foreign('id_alat', 'fk_form_alat_alat')->references('id')->on('alat')->cascadeOnUpdate()->nullOnDelete();
            $table->foreign('id_template_form', 'fk_form_alat_template_form')->references('id')->on('template_form')->nullOnDelete();
        });

        Schema::table('riwayat_pengecekan', function (Blueprint $table) {
            $table->foreign('id_form_alat', 'fk_riwayat_form_alat')->references('id')->on('form_alat')->nullOnDelete();
            $table->foreign('id_pengguna', 'fk_riwayat_pengguna')->references('id')->on('pengguna')->nullOnDelete();
        });

        Schema::table('sesi_pengguna', function (Blueprint $table) {
            $table->foreign('id_pengguna', 'fk_sesi_pengguna')->references('id')->on('pengguna')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations. Fails while form_alat or riwayat rows still have a null relation.
     */
    public function down(): void
    {
        $this->hapusForeignKey();

        Schema::table('form_alat', function (Blueprint $table) {
            $table->string('id_alat', 20)->nullable(false)->change();
            $table->unsignedBigInteger('id_template_form')->nullable(false)->change();
        });

        Schema::table('riwayat_pengecekan', function (Blueprint $table) {
            $table->unsignedBigInteger('id_form_alat')->nullable(false)->change();
        });

        Schema::table('form_alat', function (Blueprint $table) {
            $table->foreign('id_alat', 'fk_form_alat_alat')->references('id')->on('alat')->cascadeOnUpdate();
            $table->foreign('id_template_form', 'fk_form_alat_template_form')->references('id')->on('template_form');
        });

        Schema::table('riwayat_pengecekan', function (Blueprint $table) {
            $table->foreign('id_form_alat', 'fk_riwayat_form_alat')->references('id')->on('form_alat');
            $table->foreign('id_pengguna', 'fk_riwayat_pengguna')->references('id')->on('pengguna');
        });

        Schema::table('sesi_pengguna', function (Blueprint $table) {
            $table->foreign('id_pengguna', 'fk_sesi_pengguna')->references('id')->on('pengguna');
        });
    }

    /**
     * Drop every foreign key this migration redefines, before their columns change.
     */
    protected function hapusForeignKey(): void
    {
        Schema::table('form_alat', function (Blueprint $table) {
            $table->dropForeign($this->foreignKey('fk_form_alat_alat', 'id_alat'));
            $table->dropForeign($this->foreignKey('fk_form_alat_template_form', 'id_template_form'));
        });

        Schema::table('riwayat_pengecekan', function (Blueprint $table) {
            $table->dropForeign($this->foreignKey('fk_riwayat_form_alat', 'id_form_alat'));
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
