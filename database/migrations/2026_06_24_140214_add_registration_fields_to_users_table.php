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
            if (!Schema::hasColumn('users', 'prenom')) {
                $table->string('prenom')->nullable()->after('id');
            }
            if (!Schema::hasColumn('users', 'nom')) {
                $table->string('nom')->nullable()->after('prenom');
            }
            if (!Schema::hasColumn('users', 'matricule')) {
                $table->string('matricule')->unique()->nullable()->after('nom');
            }
            if (!Schema::hasColumn('users', 'universite')) {
                $table->string('universite')->nullable()->after('email');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['prenom', 'nom', 'matricule', 'universite']);
        });
    }
};
