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
        'message_json',
        'questions_json',
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
