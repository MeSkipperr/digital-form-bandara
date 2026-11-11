<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('riwayat_pengecekan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_form_alat')->constrained('form_alat', indexName: 'fk_riwayat_form_alat');
            $table->string('id_pengguna', 20)->nullable();
            $table->text('catatan')->nullable();
            $table->enum('status', ['bagus', 'kurang_bagus', 'tertunda', 'terlewati']);
            $table->timestamp('dicek_pada')->nullable();

            $table->foreign('id_pengguna', 'fk_riwayat_pengguna')->references('id')->on('pengguna');
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
