<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tableName = config('filemanager.table_name', 'media');
        
        Schema::create($tableName, function (Blueprint $table) {
            $table->id();
            $table->string('name')->index();
            $table->string('original_name');
            $table->string('path')->unique();
            $table->unsignedBigInteger('size')->nullable()->index();
            $table->string('mime_type', 100)->index();
            $table->string('extension', 10)->index();
            $table->string('disk', 50)->default('public');
            $table->string('thumbnail_path')->nullable();
            $table->string('alt_text')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            // Add indexes for better performance
            $table->index(['mime_type', 'created_at']);
            $table->index(['extension', 'created_at']);
            $table->index(['size', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $tableName = config('filemanager.table_name', 'media');
        Schema::dropIfExists($tableName);
    }
}
