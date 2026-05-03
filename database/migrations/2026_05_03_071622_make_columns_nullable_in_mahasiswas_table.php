<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeColumnsNullableInMahasiswasTable extends Migration
{
    public function up()
    {
        Schema::table('mahasiswas', function (Blueprint $table) {
            $table->string('tempat_lahir')->nullable()->change();
            $table->date('tanggal_lahir')->nullable()->change();
            $table->text('alamat')->nullable()->change();
            $table->string('no_hp')->nullable()->change();
            $table->date('tanggal_masuk')->nullable()->change();
            $table->decimal('ipk', 3, 2)->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('mahasiswas', function (Blueprint $table) {
            $table->string('tempat_lahir')->nullable(false)->change();
            $table->date('tanggal_lahir')->nullable(false)->change();
            $table->text('alamat')->nullable(false)->change();
            $table->string('no_hp')->nullable(false)->change();
            $table->date('tanggal_masuk')->nullable(false)->change();
            $table->decimal('ipk', 3, 2)->nullable(false)->change();
        });
    }
}