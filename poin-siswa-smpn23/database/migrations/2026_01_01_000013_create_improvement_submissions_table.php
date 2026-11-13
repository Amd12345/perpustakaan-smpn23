<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('improvement_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('improvement_task_id')->constrained()->restrictOnDelete();
            $table->foreignId('teacher_id')->nullable()->constrained()->nullOnDelete();
            $table->string('evidence_path')->nullable();
            $table->text('note')->nullable();
            $table->enum('status', ['menunggu', 'disetujui', 'ditolak', 'revisi'])->default('menunggu');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
            $table->index(['improvement_task_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('improvement_submissions');
    }
};
