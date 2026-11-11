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
        Schema::create('form_alat', function (Blueprint $table) {
            $table->id();
            $table->string('id_alat', 20);
            $table->foreignId('id_template_form')->constrained('template_form', indexName: 'fk_form_alat_template_form');

            $table->foreign('id_alat', 'fk_form_alat_alat')->references('id')->on('alat');
            $table->unique(['id_alat', 'id_template_form'], 'uq_alat_template_form');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_alat');
    }
};
