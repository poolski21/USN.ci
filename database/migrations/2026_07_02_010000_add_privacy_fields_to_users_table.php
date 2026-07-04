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
            $table->boolean('private_documents')->default(false)->after('cv_path');
            $table->boolean('private_friends')->default(false)->after('private_documents');
            $table->boolean('private_projects')->default(false)->after('private_friends');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['private_documents', 'private_friends', 'private_projects']);
        });
    }
};
