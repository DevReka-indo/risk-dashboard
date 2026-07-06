<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('risks', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('department');
            $table->string('category')->nullable();
            $table->enum('level', ['Low', 'Medium', 'High', 'Critical']);
            $table->unsignedTinyInteger('nilai');
            $table->enum('status', ['Open', 'In Progress', 'Closed'])->default('Open');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('risks');
    }
};
