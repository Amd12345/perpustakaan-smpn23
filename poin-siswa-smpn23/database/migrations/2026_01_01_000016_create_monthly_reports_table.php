<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monthly_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained()->restrictOnDelete();
            $table->unsignedInteger('month');
            $table->unsignedInteger('year');
            $table->enum('status', ['draf', 'final', 'revisi'])->default('draf');
            $table->json('snapshot');
            $table->unsignedInteger('total_violations')->default(0);
            $table->unsignedInteger('total_achievements')->default(0);
            $table->unsignedInteger('total_violation_points')->default(0);
            $table->unsignedInteger('total_achievement_points')->default(0);
            $table->unsignedInteger('total_reductions')->default(0);
            $table->json('previous_month_totals')->nullable();
            $table->foreignId('generated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('generated_at')->nullable();
            $table->foreignId('finalized_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('finalized_at')->nullable();
            $table->unique(['academic_year_id', 'month', 'year']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monthly_reports');
    }
};
