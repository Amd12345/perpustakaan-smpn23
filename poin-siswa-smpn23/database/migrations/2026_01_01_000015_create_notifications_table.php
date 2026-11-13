<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->restrictOnDelete();
            $table->foreignId('parent_id')->constrained()->restrictOnDelete();
            $table->string('type');
            $table->text('message');
            $table->string('recipient_phone');
            $table->enum('status', ['tertunda', 'terkirim', 'gagal'])->default('tertunda');
            $table->timestamp('sent_at')->nullable();
            $table->string('provider_message_id')->nullable();
            $table->text('error')->nullable();
            $table->timestamps();
            $table->index(['student_id', 'status']);
            $table->index(['parent_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
