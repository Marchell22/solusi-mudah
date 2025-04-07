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
        Schema::create('books', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->string('author');
            $table->text('description')->nullable();
            $table->uuid('category_id');
            $table->foreign('category_id')->references('id')->on('categories');
            $table->integer('stock')->default(0);
            $table->string('cover_path')->nullable(); // Untuk file upload
            $table->boolean('is_available')->default(true);
            $table->json('additional_info')->nullable();
            $table->datetime('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes(); // Implementasi soft deletes
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
