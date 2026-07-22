<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar_public_id')->nullable()->after('avatar');
            $table->string('cover_public_id')->nullable()->after('cover_photo');
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->string('media_public_id')->nullable()->after('media_path');
        });

        Schema::table('social_messages', function (Blueprint $table) {
            $table->string('attachment_public_id')->nullable()->after('attachment_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['avatar_public_id', 'cover_public_id']);
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('media_public_id');
        });

        Schema::table('social_messages', function (Blueprint $table) {
            $table->dropColumn('attachment_public_id');
        });
    }
};
