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
          Schema::create('borrowings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('book_id');
            $table->foreign('book_id')->references('id')->on('books');
            $table->string('borrower_name');
            $table->string('borrower_email');
            $table->datetime('borrowed_at');
            $table->datetime('due_date');
            $table->datetime('returned_at')->nullable();
            $table->boolean('is_returned')->default(false);
            $table->json('notes')->nullable();
            $table->timestamps();
            $table->softDeletes(); // Implementasi soft deletes
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('borrowings');
    }
};
