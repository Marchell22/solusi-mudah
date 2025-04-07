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
        Schema::table('borrowings', function (Blueprint $table) {
            // Menambahkan kolom untuk menyimpan data yang immutable
            $table->string('book_title')->nullable()->after('book_id');
            $table->string('book_author')->nullable()->after('book_title');
            $table->string('book_category_name')->nullable()->after('book_author');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
      Schema::table('borrowings', function (Blueprint $table) {
            $table->dropColumn(['book_title', 'book_author', 'book_category_name']);
        });
    }
};
