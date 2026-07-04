<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            if (! Schema::hasColumn('groups', 'max_members')) {
                $table->unsignedInteger('max_members')->nullable()->after('visibilite');
            }
        });
    }

    public function down(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            if (Schema::hasColumn('groups', 'max_members')) {
                $table->dropColumn('max_members');
            }
        });
    }
};
