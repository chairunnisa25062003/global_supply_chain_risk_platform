<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('risk_scores', function (Blueprint $table) {
            $table->id();
            $table->string('country_name');
            $table->unsignedTinyInteger('score');
            $table->enum('level', ['low', 'medium', 'high']);

            // Rincian tiap sub-faktor
            $table->unsignedTinyInteger('weather_score');
            $table->unsignedTinyInteger('inflation_score');
            $table->unsignedTinyInteger('news_score');
            $table->unsignedTinyInteger('currency_score');

            $table->timestamps();

            $table->index(['country_name', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('risk_scores');
    }
};
