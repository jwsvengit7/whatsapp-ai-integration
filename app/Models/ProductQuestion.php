<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductQuestion extends Model
{
    protected $fillable = ['product_id', 'question'];

    /**
     * Get the product that owns the question.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
