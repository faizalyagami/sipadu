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
        Schema::create('jenis_surats', function (Blueprint $table) {
            $table->id();
            $table->string('nama_surat');
            $table->foreignId('kategori_surat_id')->constrained('kategori_surats')->cascadeOnDelete();
            $table->string('kode_surat')->unique()->nullable();
            $table->text('deskripsi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('processing_time')->default(1);
            $table->text('syarat_surat')->nullable();
            $table->longText('template_content')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jenis_surats');
    }
};
