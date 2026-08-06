<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('official_page_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('official_page_id')->constrained('official_pages')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['official_page_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('official_page_subscriptions');
    }
};
