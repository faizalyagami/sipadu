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
        Schema::create('template_surats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jenis_surat_id')->constrained('jenis_surats')->onDelete('cascade');
            $table->string('nama_template');
            $table->text('template_html');
            $table->text('template_docx')->nullable();
            $table->json('variables')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->integer('version')->default(1);
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->index(['jenis_surat_id'], 'status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('template_surats');
    }
};
