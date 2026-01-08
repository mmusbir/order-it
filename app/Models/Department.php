<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'is_active',
        'can_access_asset_resign',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'can_access_asset_resign' => 'boolean',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'department', 'name');
    }

    public function approvalLevels()
    {
        return $this->hasMany(DepartmentApprovalLevel::class);
    }

    /**
     * Get the approver user for a specific level
     */
    public function getApproverForLevel(int $level): ?User
    {
        $approvalLevel = $this->approvalLevels()
            ->where('level', $level)
            ->where('is_active', true)
            ->first();

        return $approvalLevel?->user;
    }

    /**
     * Check if a user is the approver for a specific level in this department
     */
    public function isApproverForLevel(int $userId, int $level): bool
    {
        return $this->approvalLevels()
            ->where('level', $level)
            ->where('user_id', $userId)
            ->where('is_active', true)
            ->exists();
    }
}
