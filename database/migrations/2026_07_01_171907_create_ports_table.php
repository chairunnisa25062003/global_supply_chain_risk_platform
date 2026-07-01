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
    Schema::create('ports', function (Blueprint $table) {

        $table->id();

        $table->foreignId('country_id')
              ->constrained('countries')
              ->cascadeOnDelete();

        $table->string('port_name');

        $table->decimal('latitude',10,6);

        $table->decimal('longitude',10,6);

        $table->enum('status',[
            'Normal',
            'Busy',
            'Congested'
        ])->default('Normal');

        $table->text('description')->nullable();

        $table->timestamps();

    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ports');
    }
};
