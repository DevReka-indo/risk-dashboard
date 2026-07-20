<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            // Foreign key ke tabel users (jika user dihapus, log tidak error)
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('modul'); // contoh: 'Top Risk', 'Departemen'
            $table->text('aktivitas'); // contoh: 'Mengubah nilai risiko...'
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('audit_logs');
    }
};
