<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_parents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->constrained()->cascadeOnDelete();
            $table->string('relation')->default('orang tua');
            $table->boolean('is_primary')->default(false);
            $table->unique(['student_id', 'parent_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_parents');
    }
};
