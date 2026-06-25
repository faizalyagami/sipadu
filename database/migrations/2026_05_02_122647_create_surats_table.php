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
        Schema::create('surats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswas')->onDelete('cascade');
            $table->foreignId('jenis_surat_id')->constrained('jenis_surats')->onDelete('cascade');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('content');
            $table->text('keperluan');
            $table->enum('status', ['pending','approved', 'rejected'])->default('pending');
            $table->datetime('approved_at')->nullable();
            $table->text('ttd_elektronik')->nullable();

            //field untuk surat aktif kuliah
            $table->string('nama_ortu')->nullable();
            $table->string('nik_ortu')->nullable();
            $table->string('pangkat_ortu')->nullable();
            $table->string('instansi_ortu')->nullable();
            $table->string('alamat_kantor_ortu')->nullable();
            $table->string('bukti_pembayaran')->nullable();

            // Field untuk Surat Izin Magang
            $table->enum('tipe_pengajuan', ['individu', 'kelompok'])->nullable();
            $table->string('nama_kelompok')->nullable();
            $table->string('file_ktm')->nullable();

            // Field tambahan lainnya
            $table->string('file_pendukung')->nullable();
            $table->text('data_tambahan')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surats');
    }
};
