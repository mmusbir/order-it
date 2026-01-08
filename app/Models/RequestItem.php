<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_id',
        'product_id',
        'item_name',
        'item_specs',
        'qty',
        'snap_price',
        'item_link',
        'serial_number',
        'asset_tag',
        'asset_name',
        'snipeit_asset_id',
        'is_synced',
        'synced_at',
        'synced_item_name',
        'synced_location_name',
        'synced_qty',
        'disposal_doc_path',
    ];

    protected $casts = [
        'snap_price' => 'decimal:2',
        'is_synced' => 'boolean',
        'synced_at' => 'datetime',
    ];

    public function request()
    {
        return $this->belongsTo(Request::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
