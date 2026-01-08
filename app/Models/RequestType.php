<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'allow_quantity',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'allow_quantity' => 'boolean',
    ];

    /**
     * Scope for active request types.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
