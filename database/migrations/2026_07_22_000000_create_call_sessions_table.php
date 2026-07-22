<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('call_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('room')->unique();
            $table->foreignId('caller_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('callee_id')->constrained('users')->cascadeOnDelete();
            $table->enum('type', ['audio', 'video'])->default('audio');
            $table->enum('status', ['pending', 'accepted', 'ended', 'rejected'])->default('pending');
            $table->json('offer')->nullable();
            $table->json('answer')->nullable();
            $table->json('caller_candidates')->nullable();
            $table->json('callee_candidates')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('call_sessions');
    }
};
