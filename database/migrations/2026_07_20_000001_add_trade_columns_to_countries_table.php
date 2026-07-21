<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('countries', function (Blueprint $table) {
            $table->decimal('exports', 20, 2)->nullable()->after('inflation_year');
            $table->string('exports_year', 4)->nullable()->after('exports');
            $table->decimal('imports', 20, 2)->nullable()->after('exports_year');
            $table->string('imports_year', 4)->nullable()->after('imports');
        });
    }

    public function down(): void
    {
        Schema::table('countries', function (Blueprint $table) {
            $table->dropColumn(['exports', 'exports_year', 'imports', 'imports_year']);
        });
    }
};
