<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JobListing extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'link',
        'description',
        'pub_date',
        'location',
        'salary',
        'company',
        'company_logo',
        'tags',
        'job_type',
    ];

    protected $casts = [
        'pub_date' => 'datetime',
    ];
}