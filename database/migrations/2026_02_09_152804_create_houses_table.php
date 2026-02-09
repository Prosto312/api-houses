<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('houses', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index();
            $table->unsignedTinyInteger('bedrooms')->index();
            $table->unsignedTinyInteger('bathrooms')->index();
            $table->unsignedTinyInteger('storeys')->index();
            $table->unsignedTinyInteger('garages')->index();
            $table->unsignedInteger('price')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('houses');
    }
};
