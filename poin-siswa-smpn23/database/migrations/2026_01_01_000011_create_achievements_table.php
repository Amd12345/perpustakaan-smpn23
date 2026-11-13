<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('achievements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->restrictOnDelete();
            $table->foreignId('achievement_category_id')->constrained()->restrictOnDelete();
            $table->foreignId('academic_year_id')->constrained()->restrictOnDelete();
            $table->foreignId('teacher_id')->nullable()->constrained()->nullOnDelete();
            $table->date('achievement_date');
            $table->text('description');
            $table->string('documentation_path')->nullable();
            $table->integer('point_amount');
            $table->boolean('is_public')->default(true);
            $table->timestamps();
            $table->index(['student_id', 'academic_year_id']);
            $table->index(['achievement_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('achievements');
    }
};
