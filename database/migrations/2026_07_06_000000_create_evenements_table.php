<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evenements', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->text('description');
            $table->string('categorie')->nullable();
            $table->string('image_couverture')->nullable();
            $table->enum('visibilite', ['public', 'prive'])->default('public');
            $table->string('restriction_type')->nullable();
            $table->string('restriction_id')->nullable();
            $table->dateTime('date_debut');
            $table->dateTime('date_fin');
            $table->enum('mode', ['presentiel', 'en_ligne'])->default('presentiel');
            $table->string('lieu')->nullable();
            $table->string('lien_ligne')->nullable();
            $table->unsignedInteger('places_max')->nullable();
            $table->boolean('inscription_requise')->default(true);
            $table->enum('validation_type', ['auto', 'manuelle'])->default('auto');
            $table->boolean('est_payant')->default(false);
            $table->decimal('prix', 10, 2)->nullable();
            $table->unsignedBigInteger('organisateur_id');
            $table->string('organisateur_type');
            $table->string('contact')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evenements');
    }
};
