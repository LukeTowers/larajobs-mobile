<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobAlert extends Model
{
    protected $fillable = [
        'query',
        'location',
        'salary',
    ];
}
