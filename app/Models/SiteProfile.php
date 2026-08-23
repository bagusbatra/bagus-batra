<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteProfile extends Model
{
    protected $fillable = [
        'name',
        'nickname',
        'title_id',
        'title_en',
        'tagline_id',
        'tagline_en',
        'bio_id',
        'bio_en',
        'location',
        'email',
        'phone',
        'github',
        'linkedin',
        'twitter',
        'available_for_work',
        'years_of_exp',
        'completed_projects',
        'client_satisfaction',
        'open_source_contributions',
        'avatar',
        'secondary_avatar',
    ];

    protected $casts = [
        'available_for_work' => 'boolean',
    ];

    /**
     * Singleton accessor — always returns (and creates if missing) the
     * single profile row with id = 1.
     */
    public static function current(): self
    {
        return static::firstOrCreate(['id' => 1]);
    }
}
