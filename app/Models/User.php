<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'employee_number',
        'password',
        'role',
        'approval_role_id',
        'job_title_id',
        'department',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the approval role assigned to this user.
     */
    public function approvalRole()
    {
        return $this->belongsTo(ApprovalRole::class);
    }

    /**
     * Get the job title assigned to this user.
     */
    public function jobTitle()
    {
        return $this->belongsTo(JobTitle::class);
    }

    /**
     * Get the department model for this user.
     */
    public function getDepartmentModel()
    {
        if (empty($this->department)) {
            return null;
        }
        return Department::where('name', $this->department)->first();
    }

    /**
     * Check if user can access Asset Resign feature.
     * Superadmin always has access, other users based on department setting.
     */
    public function canAccessAssetResign(): bool
    {
        if ($this->role === 'superadmin' || $this->role === 'admin') {
            return true;
        }

        $dept = $this->getDepartmentModel();
        return $dept && $dept->can_access_asset_resign;
    }
}
