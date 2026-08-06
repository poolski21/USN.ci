<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'subscription_plan')) {
                $table->enum('subscription_plan', ['standard', 'premium'])->default('standard')->after('is_certified');
            }

            if (! Schema::hasColumn('users', 'visibility_boost')) {
                $table->unsignedTinyInteger('visibility_boost')->default(0)->after('subscription_plan');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'visibility_boost')) {
                $table->dropColumn('visibility_boost');
            }
            if (Schema::hasColumn('users', 'subscription_plan')) {
                $table->dropColumn('subscription_plan');
            }
        });
    }
};
