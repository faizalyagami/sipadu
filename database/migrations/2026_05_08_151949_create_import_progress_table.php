<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImportProgressTable extends Migration
{
    public function up()
    {
        Schema::create('import_progress', function (Blueprint $table) {
            $table->id();
            $table->string('batch_id')->unique();
            $table->string('filename');
            $table->integer('total_rows')->default(0);
            $table->integer('processed_rows')->default(0);
            $table->integer('success_rows')->default(0);
            $table->integer('failed_rows')->default(0);
            $table->string('status')->default('processing');
            $table->json('errors')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('import_progress');
    }
}