<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ports', function (Blueprint $table) {
           
            $table->enum('status', ['Normal', 'Busy', 'Congested'])
                  ->default('Normal')
                  ->after('harbor_size');
        });
    }

    public function down(): void
    {
        Schema::table('ports', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
