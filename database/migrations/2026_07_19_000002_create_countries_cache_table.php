<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('countries');

        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('official_name')->nullable();
            $table->string('capital')->nullable();
            $table->string('region')->nullable();
            $table->string('subregion')->nullable();
            $table->unsignedBigInteger('population')->nullable();
            $table->string('currency_code', 10)->nullable();
            $table->string('currency_name')->nullable();
            $table->string('languages')->nullable();
            $table->string('flag')->nullable();
            $table->string('iso2', 2)->nullable();
            $table->string('iso3', 3)->nullable();
            $table->decimal('gdp', 20, 2)->nullable();
            $table->string('gdp_year', 4)->nullable();
            $table->decimal('inflation', 8, 4)->nullable();
            $table->string('inflation_year', 4)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
