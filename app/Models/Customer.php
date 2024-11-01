<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Customer extends Model
{
    use HasFactory;

    protected $table = 'customers';


    protected $fillable = [
        'name',
        'phone',
        'email',
        'conversation_stage',
        'has_completed_onboarding',
        'location',
        'message_json',
        'questions_json',
        'product_branch',
        'message_branch',
        'current_question_index'
    ];


    /**
     * Get the conversations for the customer.
     */
    public function conversations(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Conversation::class);
    }
}
