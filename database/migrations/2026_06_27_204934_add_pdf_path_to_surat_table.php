<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPdfPathToSuratTable extends Migration
{
    public function up()
    {
        Schema::table('surats', function (Blueprint $table) {
            $table->string('pdf_path')->nullable()->after('content');
            $table->text('html_content')->nullable()->after('pdf_path');
        });
    }

    public function down()
    {
        Schema::table('surats', function (Blueprint $table) {
            $table->dropColumn(['pdf_path', 'html_content']);
        });
    }
}
