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
        Schema::create('template_form', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_kategori_alat')->constrained('kategori_alat', indexName: 'fk_template_form_kategori');
            $table->string('path_gambar')->nullable();
            $table->string('nama', 100);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('template_form');
    }
};
