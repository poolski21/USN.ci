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
            if (!Schema::hasColumn('users', 'bio')) {
                $table->text('bio')->nullable()->after('cover_photo');
            }
            if (!Schema::hasColumn('users', 'cv_url')) {
                $table->string('cv_url')->nullable()->after('bio');
            }
            if (!Schema::hasColumn('users', 'cv_path')) {
                $table->string('cv_path')->nullable()->after('cv_url');
            }
            if (!Schema::hasColumn('users', 'github')) {
                $table->string('github')->nullable()->after('cv_path');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'github')) {
                $table->dropColumn('github');
            }
            if (Schema::hasColumn('users', 'cv_path')) {
                $table->dropColumn('cv_path');
            }
            if (Schema::hasColumn('users', 'cv_url')) {
                $table->dropColumn('cv_url');
            }
            if (Schema::hasColumn('users', 'bio')) {
                $table->dropColumn('bio');
            }
        });
    }
};
