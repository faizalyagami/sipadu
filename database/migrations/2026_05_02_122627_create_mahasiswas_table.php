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
        Schema::create('mahasiswas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('prodi_id')->constrained('prodis');
            $table->string('npm')->unique();
            $table->string('nama_lengkap');
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->text('alamat');
            $table->string('no_hp', 15);
            $table->date('tanggal_masuk');
            $table->enum('status_mahasiswa', ['Aktif', 'Lulus', 'Cuti'])->default('Aktif');
            $table->decimal('ipk', 3, 2)->nullable();
            $table->string('dosen_wali')->nullable();
            $table->string('dosen_wali_nik')->nullable();
            $table->integer('sks_tempuh')->nullable();
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mahasiswas');
    }
};
