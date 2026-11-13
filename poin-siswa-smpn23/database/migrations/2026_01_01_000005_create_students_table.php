<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained()->restrictOnDelete();
            $table->foreignId('academic_year_id')->constrained()->restrictOnDelete();
            $table->string('nisn')->unique();
            $table->string('name');
            $table->enum('gender', ['L', 'P']);
            $table->date('birth_date')->nullable();
            $table->text('address')->nullable();
            $table->enum('status', ['aktif', 'nonaktif', 'lulus', 'pindah'])->default('aktif');
            $table->timestamps();
            $table->index(['class_id', 'status']);
            $table->index(['academic_year_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
