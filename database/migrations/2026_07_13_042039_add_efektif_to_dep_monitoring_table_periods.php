<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dep_monitoring_periods', function (Blueprint $table) {
            $table->enum('efektif_risiko', [
                'Pencatatan',
                'Effective',
                'Mostly Effective',
                'Partially Effective',
                'In-Effective',
                'Unmeasurable'
            ])
            ->default('Pencatatan')
            ->after('penanganan');
        });
    }

    public function down(): void
    {
        Schema::table('dep_monitoring_periods', function (Blueprint $table) {
            $table->dropColumn('efektif_risiko');
        });
    }
};
