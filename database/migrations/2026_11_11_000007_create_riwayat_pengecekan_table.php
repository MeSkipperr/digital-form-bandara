<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Deleting a form_alat or pengguna keeps the riwayat as history with a null relation. The username can be
     * changed, so the riwayat follows the new username.
     */
    public function up(): void
    {
        Schema::create('riwayat_pengecekan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_form_alat')->nullable()->constrained('form_alat', indexName: 'fk_riwayat_form_alat')->nullOnDelete();
            $table->string('id_pengguna', 20)->nullable();
            $table->text('catatan')->nullable();
            $table->enum('status', ['bagus', 'kurang_bagus', 'tertunda', 'terlewati'])->default('tertunda');
            $table->timestamp('dicek_pada')->nullable();

            $table->foreign('id_pengguna', 'fk_riwayat_pengguna')->references('id')->on('pengguna')->cascadeOnUpdate()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_pengecekan');
    }
};
