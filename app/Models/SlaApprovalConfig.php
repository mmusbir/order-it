<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SlaApprovalConfig extends Model
{
    protected $fillable = [
        'approval_level',
        'target_hours',
        'warning_percent',
        'escalation_percent',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get config by approval level
     */
    public static function getByLevel(int $level): ?self
    {
        return static::where('approval_level', $level)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Get all active configs ordered by level
     */
    public static function getAllActive()
    {
        return static::where('is_active', true)
            ->orderBy('approval_level')
            ->get();
    }

    /**
     * Get level label
     */
    public function getLevelLabelAttribute(): string
    {
        $labels = [
            1 => 'Level 1 - SPV',
            2 => 'Level 2 - Manager',
            3 => 'Level 3 - Head',
            4 => 'Level 4 - Director',
        ];
        return $labels[$this->approval_level] ?? "Level {$this->approval_level}";
    }
}
