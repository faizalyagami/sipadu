<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSignatureFieldsToSuratsTable extends Migration
{
    public function up()
    {
        Schema::table('surats', function (Blueprint $table) {
            $table->string('nomor_surat')->nullable()->after('id');
            $table->string('ttd_nama')->nullable()->after('ttd_elektronik');
            $table->string('ttd_nip')->nullable()->after('ttd_nama');
            $table->string('ttd_jabatan')->nullable()->after('ttd_nip');
            $table->text('alasan_reject')->nullable()->after('status');
        });
    }

    public function down()
    {
        Schema::table('surats', function (Blueprint $table) {
            $table->dropColumn(['nomor_surat', 'ttd_nama', 'ttd_nip', 'ttd_jabatan', 'alasan_reject']);
        });
    }
}