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
        Schema::create('top_risiko_unit_kerja', function (Blueprint $table): void {
            $table->id('id_risiko_unit');

            $table->foreignId('id_risiko')
                ->constrained('top_risiko', 'id_risiko')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('id_unit')
                ->constrained('top_unit_kerja', 'id_unit')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->timestamps();

            $table->unique(
                ['id_risiko', 'id_unit'],
                'top_risiko_unit_unique'
            );

            $table->index('id_risiko');
            $table->index('id_unit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('top_risiko_unit_kerja');
    }
};
