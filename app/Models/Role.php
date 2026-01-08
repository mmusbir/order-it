<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'approval_level',
        'is_approver',
        'is_system',
        'is_active',
    ];

    protected $casts = [
        'is_approver' => 'boolean',
        'is_system' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get users with this role.
     */
    public function users()
    {
        return $this->hasMany(User::class, 'role', 'slug');
    }

    /**
     * Scope for active roles.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for approver roles.
     */
    public function scopeApprovers($query)
    {
        return $query->where('is_approver', true);
    }

    /**
     * Scope for roles by approval level.
     */
    public function scopeByLevel($query, $level)
    {
        return $query->where('approval_level', $level);
    }
}
