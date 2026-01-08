<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_no',
        'requester_id',
        'status',
        'priority',
        'po_number',
        'courier',
        'tracking_no',
        'bast_file',
        'request_type',
        'replacement_reason',
        'disposal_doc_path',
        'beneficiary_type',
        'beneficiary_id',
        'beneficiary_name',
        'shipping_address',
        'shipping_pic_name',
        'shipping_pic_phone',
        'asset_photo_path',
        'e_form_confirmed_at',
    ];

    // Status Constants
    const STATUS_DRAFT = 'DRAFT';
    const STATUS_SUBMITTED = 'SUBMITTED';
    const STATUS_APPROVED_1 = 'APPR_1';
    const STATUS_APPROVED_2 = 'APPR_2';
    const STATUS_APPROVED_3 = 'APPR_3';
    const STATUS_APPROVED_4 = 'APPR_4';
    const STATUS_PO_ISSUED = 'PO_ISSUED';
    const STATUS_ON_DELIVERY = 'ON_DELIVERY';
    const STATUS_COMPLETED = 'COMPLETED';
    const STATUS_SYNCED = 'SYNCED';
    const STATUS_REJECTED = 'REJECTED';

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function items()
    {
        return $this->hasMany(RequestItem::class);
    }

    public function logs()
    {
        return $this->hasMany(ApprovalLog::class);
    }

    public function approvalLogs()
    {
        return $this->hasMany(ApprovalLog::class);
    }

    public function approvers()
    {
        return $this->hasMany(RequestApprover::class);
    }

    public function getTotalAmountAttribute()
    {
        return $this->items->sum(function ($item) {
            return $item->qty * $item->snap_price;
        });
    }
}
