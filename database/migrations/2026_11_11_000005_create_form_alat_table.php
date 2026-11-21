<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Deleting an alat or template form keeps the form_alat with a null relation, so riwayat pengecekan stays as
     * history. The alat id can be renamed, so it cascades on update.
     */
    public function up(): void
    {
        Schema::create('form_alat', function (Blueprint $table) {
            $table->id();
            $table->string('id_alat', 20)->nullable();
            $table->foreignId('id_template_form')->nullable()->constrained('template_form', indexName: 'fk_form_alat_template_form')->nullOnDelete();

            $table->foreign('id_alat', 'fk_form_alat_alat')->references('id')->on('alat')->cascadeOnUpdate()->nullOnDelete();
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
