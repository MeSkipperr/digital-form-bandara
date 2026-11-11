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
        Schema::create('alat', function (Blueprint $table) {
            $table->string('id', 20)->primary();
            $table->foreignId('id_kategori_alat')->constrained('kategori_alat', indexName: 'fk_alat_kategori');
            $table->enum('status', ['bagus', 'perlu_perbaikan']);
            $table->string('model', 100)->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('lokasi', 100)->nullable();
            $table->timestamp('dibuat_pada')->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alat');
    }
};
