<?php
// app/Models/Category.php
namespace App\Models;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Category extends Model
{
    use HasFactory, SoftDeletes, HasUuids, Auditable;

    protected $fillable = ['name', 'description', 'is_active', 'metadata'];

    protected $casts = [
        'is_active' => 'boolean',
        'metadata' => 'array',
    ];

    protected $auditableFields = ['name', 'description', 'is_active', 'metadata'];


    /**
     * Get all books for the category
     */
    public function books()
    {
        return $this->hasMany(Book::class);
    }
}
