<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $fillable = [
        'testimonial_key',
        'name',
        'role',
        'company',
        'avatar',
        'content',
        'rating',
        'project_tag',
        'sort_order',
    ];
}
