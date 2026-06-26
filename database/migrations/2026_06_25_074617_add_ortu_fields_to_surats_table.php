<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('surats', function (Blueprint $table) {
            $table->string('pekerjaan_ortu')->nullable()->after('alamat_kantor_ortu');
            $table->string('no_hp_ortu')->nullable()->after('pekerjaan_ortu');
        });
    }

    public function down(): void
    {
        Schema::table('surats', function (Blueprint $table) {
            $table->dropColumn([
                'nama_ortu',
                'nik_ortu',
                'pangkat_ortu',
                'instansi_ortu',
                'alamat_kantor_ortu',
                'pekerjaan_ortu',
                'no_hp_ortu'
            ]);
        });
    }
};
