<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('ports');

        Schema::create('ports', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('country');
            $table->string('unlocode', 10)->nullable();
            $table->decimal('latitude', 10, 6);
            $table->decimal('longitude', 10, 6);
            $table->string('harbor_size')->nullable();
            $table->timestamps();

            $table->index('country');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ports');
    }
};
