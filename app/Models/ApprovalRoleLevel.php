<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovalRoleLevel extends Model
{
    use HasFactory;

    protected $fillable = [
        'approval_role_id',
        'level',
        'user_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the approval role this level belongs to.
     */
    public function approvalRole()
    {
        return $this->belongsTo(ApprovalRole::class);
    }

    /**
     * Get the user assigned to this level.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the level label (e.g., "Level 1", "Level 2").
     */
    public function getLevelLabelAttribute(): string
    {
        return "Level {$this->level}";
    }
}
