<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('violations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->restrictOnDelete();
            $table->foreignId('violation_category_id')->constrained()->restrictOnDelete();
            $table->foreignId('academic_year_id')->constrained()->restrictOnDelete();
            $table->foreignId('teacher_id')->nullable()->constrained()->nullOnDelete();
            $table->date('incident_date');
            $table->text('description');
            $table->string('evidence_path')->nullable();
            $table->integer('point_amount');
            $table->enum('status', ['aktif', 'dibatalkan'])->default('aktif');
            $table->timestamps();
            $table->index(['student_id', 'academic_year_id']);
            $table->index(['incident_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('violations');
    }
};
