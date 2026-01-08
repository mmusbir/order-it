<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SlaFulfillmentConfig extends Model
{
    protected $fillable = [
        'priority',
        'response_hours',
        'fulfillment_hours',
        'warning_percent',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get config by priority
     */
    public static function getByPriority(string $priority): ?self
    {
        return static::where('priority', $priority)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Get all configs ordered by priority severity
     */
    public static function getAllOrdered()
    {
        return static::orderByRaw("FIELD(priority, 'urgent', 'high', 'medium', 'low')")
            ->get();
    }

    /**
     * Get priority label with code
     */
    public function getPriorityLabelAttribute(): string
    {
        $labels = [
            'urgent' => 'P1 - Critical',
            'high' => 'P2 - High',
            'medium' => 'P3 - Medium',
            'low' => 'P4 - Low',
        ];
        return $labels[$this->priority] ?? ucfirst($this->priority);
    }

    /**
     * Get total target hours (response + fulfillment)
     */
    public function getTotalHoursAttribute(): int
    {
        return $this->response_hours + $this->fulfillment_hours;
    }
}
