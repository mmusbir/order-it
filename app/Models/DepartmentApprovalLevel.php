<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepartmentApprovalLevel extends Model
{
    use HasFactory;

    // Max Levels Constant
    const MAX_LEVELS = 10;
    const DEFAULT_LEVELS = 4;

    // Legacy Level Constants (for backward compatibility)
    const LEVEL_SPV = 1;
    const LEVEL_MANAGER = 2;
    const LEVEL_HEAD = 3;
    const LEVEL_DIRECTOR = 4;

    protected $fillable = [
        'department_id',
        'level',
        'user_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getLevelLabelAttribute()
    {
        return 'Level ' . $this->level;
    }
}
