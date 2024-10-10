<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorProduct extends Model
{
    use HasFactory;

    protected $table = 'vendor_product';

    protected $fillable = [
        'vendor_id',
        'product_id',
        'is_vendor',
        'date'
    ];

    /**
     * Get the product that owns the vendor product.
     */
    public function product(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the user (vendor) that owns the vendor product.
     */
    public function vendor(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }
}
