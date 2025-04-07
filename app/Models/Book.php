<?php

namespace App\Models;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Book extends Model
{
    use HasFactory, SoftDeletes, HasUuids, Auditable;

    protected $fillable = [
        'title', 'author', 'description', 'category_id', 
        'stock', 'cover_path', 'is_available', 'additional_info',
        'published_at'
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'additional_info' => 'array',
        'published_at' => 'datetime',
    ];

    /**
     * Get the category that owns the book
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the borrowings for the book
     */
    public function borrowings()
    {
        return $this->hasMany(Borrowing::class);
    }
}
