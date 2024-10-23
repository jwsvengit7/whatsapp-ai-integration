<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $table = 'conversations';

    protected $fillable = [
        'customer_id',
        'message',
        'is_from_customer',
        'status',
        'timestamp',
        'chart_branch',
        'date',
        'message_date'
    ];



    /**
     * Get the customer that owns the conversation.
     */
    public function customer(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}

