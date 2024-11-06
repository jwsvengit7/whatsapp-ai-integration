<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AI extends Model
{
    use HasFactory;
    protected $table = 'ai_table';

    protected $fillable = [
        'url',
        'context'
    ];

}
