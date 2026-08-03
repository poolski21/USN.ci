<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_certified')->default(false);
            $table->string('certification_status')->default('none');
            $table->string('certified_university')->nullable();
            $table->string('certification_package')->nullable();
        });

        Schema::create('certification_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('university');
            $table->string('package');
            $table->string('payment_status')->default('paid');
            $table->string('status')->default('pending');
            $table->text('notes')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certification_requests');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_certified', 'certification_status', 'certified_university', 'certification_package']);
        });
    }
};
