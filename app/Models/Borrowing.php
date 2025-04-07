<?php

namespace App\Models;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Borrowing extends Model
{
    use HasFactory, SoftDeletes, HasUuids, Auditable;

    protected $fillable = [
        'book_id', 'borrower_name', 'borrower_email',
        'borrowed_at', 'due_date', 'returned_at', 'is_returned', 'notes',
        'book_title', 'book_author', 'book_category_name'
    ];

    protected $casts = [
        'borrowed_at' => 'datetime',
        'due_date' => 'datetime',
        'returned_at' => 'datetime',
        'is_returned' => 'boolean',
        'notes' => 'array',
    ];

    /**
     * Get the book that is borrowed
     */
    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * Menyimpan data buku secara immutable saat peminjaman dibuat
     */
    public static function boot()
    {
        parent::boot();

        // Saat peminjaman baru dibuat
        static::creating(function ($borrowing) {
            if ($borrowing->book_id) {
                $book = Book::find($borrowing->book_id);
                if ($book) {
                    $borrowing->book_title = $book->title;
                    $borrowing->book_author = $book->author;
                    $borrowing->book_category_name = $book->category ? $book->category->name : null;
                }
            }
        });
    }
}