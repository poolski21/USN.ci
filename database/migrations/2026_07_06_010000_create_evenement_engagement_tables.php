<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evenement_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evenement_id')->constrained('evenements')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['evenement_id', 'user_id']);
        });

        Schema::create('evenement_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evenement_id')->constrained('evenements')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('contenu');
            $table->timestamps();
        });

        Schema::table('evenements', function (Blueprint $table) {
            $table->unsignedInteger('likes_count')->default(0)->after('contact');
            $table->unsignedInteger('comments_count')->default(0)->after('likes_count');
            $table->unsignedInteger('shares_count')->default(0)->after('comments_count');
        });
    }

    public function down(): void
    {
        Schema::table('evenements', function (Blueprint $table) {
            $table->dropColumn(['likes_count', 'comments_count', 'shares_count']);
        });

        Schema::dropIfExists('evenement_comments');
        Schema::dropIfExists('evenement_likes');
    }
};
