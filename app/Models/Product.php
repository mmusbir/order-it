<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku',
        'name',
        'specs',
        'image',
        'category',
        'model_name',
        'snipeit_model_id',
        'snipeit_category_id',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->sku)) {
                // Generate SKU: PRD-0001, PRD-0002, etc.
                $lastProduct = self::orderBy('id', 'desc')->first();
                $nextNumber = ($lastProduct ? $lastProduct->id : 0) + 1;
                $product->sku = 'PRD-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    /**
     * Get the request types that this product is available for.
     */
    public function requestTypes()
    {
        return $this->belongsToMany(RequestType::class, 'product_request_type');
    }

    /**
     * Scope to filter products by request type slug.
     */
    public function scopeForRequestType($query, $requestTypeSlug)
    {
        return $query->whereHas('requestTypes', function ($q) use ($requestTypeSlug) {
            $q->where('slug', $requestTypeSlug);
        });
    }
}
