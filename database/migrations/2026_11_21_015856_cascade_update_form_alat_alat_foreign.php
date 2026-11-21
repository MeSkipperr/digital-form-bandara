<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The alat ID can be changed from the Ubah Alat modal, so the forms assigned to it follow the new ID.
     */
    public function up(): void
    {
        Schema::table('form_alat', function (Blueprint $table) {
            $table->dropForeign($this->foreignKeyLama());
            $table->foreign('id_alat', 'fk_form_alat_alat')->references('id')->on('alat')->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('form_alat', function (Blueprint $table) {
            $table->dropForeign($this->foreignKeyLama());
            $table->foreign('id_alat', 'fk_form_alat_alat')->references('id')->on('alat');
        });
    }

    /**
     * SQLite cannot drop a foreign key by name, only by its columns.
     *
     * @return string|list<string>
     */
    protected function foreignKeyLama(): string|array
    {
        return Schema::getConnection()->getDriverName() === 'sqlite' ? ['id_alat'] : 'fk_form_alat_alat';
    }
};
