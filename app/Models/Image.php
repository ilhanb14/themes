<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Import BelongsTo

class Image extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * Protects against mass assignment vulnerabilities.
     * Only these fields can be filled using Image::create() or $image->fill().
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'path',
    ];

    /**
     * Get the user that owns the image.
     * Defines the inverse of the one-to-many relationship.
     */
    public function user(): BelongsTo // Type hint the return type
    {
        // Assumes foreign key is 'user_id' and related key is 'id' on users table
        return $this->belongsTo(User::class); 
    }
}