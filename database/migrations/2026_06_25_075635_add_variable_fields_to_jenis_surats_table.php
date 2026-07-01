<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jenis_surats', function (Blueprint $table) {
            $table->json('variable_fields')->nullable()->after('template_content');
            $table->json('variable_options')->nullable()->after('variable_fields');
        });
    }

    public function down(): void
    {
        Schema::table('jenis_surats', function (Blueprint $table) {
            $table->dropColumn(['variable_fields', 'variable_options']);
        });
    }
};
