<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResignedAsset extends Model
{
    protected $fillable = [
        'snipeit_asset_id',
        'asset_tag',
        'asset_name',
        'serial_number',
        'model_name',
        'category_name',
        'location_name',
        'previous_employee_number',
        'previous_employee_name',
        'status',
        'assigned_to_user_id',
        'assigned_to_snipeit_user_id',
        'assigned_to_name',
        'checked_out_at',
    ];

    protected $casts = [
        'checked_out_at' => 'datetime',
    ];

    /**
     * Get the user this asset is checked out to.
     */
    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }

    /**
     * Check if asset is available for checkout.
     */
    public function isAvailable(): bool
    {
        return $this->status === 'available';
    }

    /**
     * Check if asset is checked out.
     */
    public function isCheckedOut(): bool
    {
        return $this->status === 'checked_out';
    }
}
