<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovalRole extends Model
{
    use HasFactory;

    const MAX_LEVELS = 10;
    const DEFAULT_LEVELS = 4;

    protected $fillable = [
        'name',
        'code',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the approval levels for this role.
     */
    public function levels()
    {
        return $this->hasMany(ApprovalRoleLevel::class)->orderBy('level');
    }

    /**
     * Get the users assigned to this approval role.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
