<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('weather_cache', function (Blueprint $table) {
            $table->id();
            $table->string('location_name');
            $table->decimal('latitude', 10, 6)->nullable();
            $table->decimal('longitude', 10, 6)->nullable();
            $table->decimal('temperature', 5, 2)->nullable();
            $table->string('condition')->nullable();
            $table->decimal('precipitation', 6, 2)->nullable();
            $table->decimal('wind_speed', 6, 2)->nullable();
            $table->boolean('is_storm')->default(false);
            $table->timestamp('fetched_at');
            $table->timestamps();

            $table->index('location_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weather_cache');
    }
};
