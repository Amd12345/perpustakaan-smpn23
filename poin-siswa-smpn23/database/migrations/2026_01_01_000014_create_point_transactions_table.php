<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('point_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->restrictOnDelete();
            $table->foreignId('academic_year_id')->constrained()->restrictOnDelete();
            $table->enum('type', ['pelanggaran', 'prestasi', 'pengurangan', 'konversi', 'konversi_prestasi', 'penyesuaian']);
            $table->enum('balance_type', ['pelanggaran', 'prestasi']);
            $table->string('source_type');
            $table->unsignedBigInteger('source_id');
            $table->integer('points');
            $table->integer('balance_before');
            $table->integer('balance_after');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('description')->nullable();
            $table->timestamp('transaction_date');
            $table->unique(['source_type', 'source_id', 'type']);
            $table->index(['student_id', 'academic_year_id', 'type']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('point_transactions');
    }
};
