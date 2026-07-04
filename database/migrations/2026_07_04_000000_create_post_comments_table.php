<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('post_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('posts')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('contenu');
            $table->timestamps();
        });

        Schema::table('posts', function (Blueprint $table) {
            if (!Schema::hasColumn('posts', 'shares_count')) {
                $table->unsignedInteger('shares_count')->default(0)->after('comments_count');
            }
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            if (Schema::hasColumn('posts', 'shares_count')) {
                $table->dropColumn('shares_count');
            }
        });

        Schema::dropIfExists('post_comments');
    }
};
