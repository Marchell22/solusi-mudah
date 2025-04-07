<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Borrowing extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $fillable = [
        'book_id', 'borrower_name', 'borrower_email',
        'borrowed_at', 'due_date', 'returned_at', 'is_returned', 'notes'
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
}