<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('point_rules', function (Blueprint $table) {
            $table->id();
            $table->string('ruleable_type');
            $table->unsignedBigInteger('ruleable_id');
            $table->foreignId('academic_year_id')->constrained()->restrictOnDelete();
            $table->integer('point_value');
            $table->boolean('is_active')->default(true);
            $table->unique(['ruleable_type', 'ruleable_id', 'academic_year_id']);
            $table->index(['ruleable_type', 'ruleable_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('point_rules');
    }
};
