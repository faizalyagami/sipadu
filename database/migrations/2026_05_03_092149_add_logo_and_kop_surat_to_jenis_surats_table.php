<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLogoAndKopSuratToJenisSuratsTable extends Migration
{
    public function up()
    {
        Schema::table('jenis_surats', function (Blueprint $table) {
            $table->string('logo_path')->nullable()->after('template_content');
            $table->string('kop_surat_path')->nullable()->after('logo_path');
            $table->text('kop_html')->nullable()->after('kop_surat_path');
        });
    }

    public function down()
    {
        Schema::table('jenis_surats', function (Blueprint $table) {
            $table->dropColumn(['logo_path', 'kop_surat_path', 'kop_html']);
        });
    }
}