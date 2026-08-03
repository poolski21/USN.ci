<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'is_certified')) {
                $table->boolean('is_certified')->default(false)->after('remember_token');
            }
            if (! Schema::hasColumn('users', 'certified_via')) {
                $table->string('certified_via')->nullable()->after('is_certified');
            }
            if (! Schema::hasColumn('users', 'certified_at')) {
                $table->timestamp('certified_at')->nullable()->after('certified_via');
            }
        });

        Schema::table('certification_requests', function (Blueprint $table) {
            if (! Schema::hasColumn('certification_requests', 'reference')) {
                $table->string('reference')->unique()->after('id');
            }
            if (! Schema::hasColumn('certification_requests', 'provider_transaction_id')) {
                $table->string('provider_transaction_id')->nullable()->after('status');
            }
            if (! Schema::hasColumn('certification_requests', 'amount')) {
                $table->unsignedInteger('amount')->default(25000)->after('provider_transaction_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('certification_requests', function (Blueprint $table) {
            if (Schema::hasColumn('certification_requests', 'amount')) {
                $table->dropColumn('amount');
            }
            if (Schema::hasColumn('certification_requests', 'provider_transaction_id')) {
                $table->dropColumn('provider_transaction_id');
            }
            if (Schema::hasColumn('certification_requests', 'reference')) {
                $table->dropUnique(['reference']);
                $table->dropColumn('reference');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'certified_at')) {
                $table->dropColumn('certified_at');
            }
            if (Schema::hasColumn('users', 'certified_via')) {
                $table->dropColumn('certified_via');
            }
            if (Schema::hasColumn('users', 'is_certified')) {
                $table->dropColumn('is_certified');
            }
        });
    }
};
