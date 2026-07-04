<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('social_messages', function (Blueprint $table) {
            if (! Schema::hasColumn('social_messages', 'attachment_path')) {
                $table->string('attachment_path')->nullable()->after('body');
            }
            if (! Schema::hasColumn('social_messages', 'attachment_type')) {
                $table->string('attachment_type')->nullable()->after('attachment_path');
            }
            if (! Schema::hasColumn('social_messages', 'attachment_name')) {
                $table->string('attachment_name')->nullable()->after('attachment_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('social_messages', function (Blueprint $table) {
            if (Schema::hasColumn('social_messages', 'attachment_name')) {
                $table->dropColumn('attachment_name');
            }
            if (Schema::hasColumn('social_messages', 'attachment_type')) {
                $table->dropColumn('attachment_type');
            }
            if (Schema::hasColumn('social_messages', 'attachment_path')) {
                $table->dropColumn('attachment_path');
            }
        });
    }
};
