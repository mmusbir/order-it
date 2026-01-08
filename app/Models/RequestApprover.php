<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestApprover extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_id',
        'level',
        'user_id',
        'status',
        'notes',
        'action_at',
    ];

    protected $casts = [
        'action_at' => 'datetime',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    /**
     * Get the request this approver is assigned to.
     */
    public function request()
    {
        return $this->belongsTo(Request::class);
    }

    /**
     * Get the user assigned as approver.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get level label
     */
    public function getLevelLabelAttribute(): string
    {
        $labels = [1 => 'SPV', 2 => 'Manager', 3 => 'Head', 4 => 'Director'];
        return $labels[$this->level] ?? "Level {$this->level}";
    }
}
