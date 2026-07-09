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
    Schema::table('smap_monitoring', function (Blueprint $table) {
        // parent_id bertipe sama dengan primary key (id_smap), boleh kosong (nullable)
        $table->unsignedBigInteger('parent_id')->nullable()->after('id_smap');

        // Opsional: Buat foreign key ke dirinya sendiri agar data integrity terjaga
        $table->foreign('parent_id')->references('id_smap')->on('smap_monitoring')->onDelete('cascade');
    });
}

public function down(): void
{
    Schema::table('smap_monitoring', function (Blueprint $table) {
        $table->dropForeign(['parent_id']);
        $table->dropColumn('parent_id');
    });
}
    };
