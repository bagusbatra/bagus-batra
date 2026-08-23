<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Experience extends Model
{
    protected $fillable = [
        'experience_key',
        'period',
        'role',
        'company',
        'location',
        'type',
        'description',
        'achievements',
        'skills',
        'featured',
        'sort_order',
    ];

    protected $casts = [
        'achievements' => 'array',
        'skills' => 'array',
        'featured' => 'boolean',
    ];
}
