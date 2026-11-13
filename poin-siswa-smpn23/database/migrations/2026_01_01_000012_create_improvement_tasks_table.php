<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('improvement_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('violation_id')->unique()->constrained()->restrictOnDelete();
            $table->foreignId('teacher_id')->nullable()->constrained()->nullOnDelete();
            $table->text('description');
            $table->integer('max_point_reduction');
            $table->date('due_date')->nullable();
            $table->enum('status', ['menunggu', 'menunggu_verifikasi', 'disetujui', 'ditolak', 'revisi'])->default('menunggu');
            $table->text('rejection_reason')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->index(['violation_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('improvement_tasks');
    }
};
