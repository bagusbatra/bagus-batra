<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{
    protected $fillable = [
        'post_key',
        'title',
        'slug',
        'summary',
        'category',
        'tags',
        'read_time',
        'published_at',
        'cover_image',
        'author_name',
        'author_role',
        'author_avatar',
        'likes',
        'views',
        'sections',
        'sort_order',
    ];

    protected $casts = [
        'tags' => 'array',
        'sections' => 'array',
    ];

    /**
     * Convert to the camelCase shape used by the front-end (Alpine) reader modal template,
     * mirroring the original TypeScript `BlogPost` interface.
     */
    public function toJs(): array
    {
        return [
            'id' => $this->post_key,
            'title' => $this->title,
            'slug' => $this->slug,
            'summary' => $this->summary,
            'category' => $this->category,
            'tags' => $this->tags,
            'readTime' => $this->read_time,
            'publishedAt' => $this->published_at,
            'coverImage' => $this->cover_image,
            'author' => [
                'name' => $this->author_name,
                'role' => $this->author_role,
                'avatar' => $this->author_avatar,
            ],
            'likes' => $this->likes,
            'views' => $this->views,
            'sections' => $this->sections,
        ];
    }
}
