<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserPreference extends Model
{
    use HasFactory;
    protected $fillable = ['preferred_sources', 'preferred_categories', 'preferred_authors'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Cast the JSON fields to arrays
    protected $casts = [
        'preferred_sources' => 'array',
        'preferred_categories' => 'array',
        'preferred_authors' => 'array',
    ];
}
