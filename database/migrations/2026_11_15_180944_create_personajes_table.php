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
        Schema::create('personajes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->integer('altura')->nullable();
            $table->integer('masa')->nullable();
            $table->string('color_cabello', 100)->nullable();
            $table->string('color_ojos', 100)->nullable();
            $table->enum('genero', ['masculino', 'femenino', 'n/a']);
            $table->string('planeta_origen', 100)->nullable();
            $table->boolean('origen_swapi')->default(false);
            $table->integer('swapi_id')->nullable()->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personajes');
    }
};
