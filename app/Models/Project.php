<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'project_key',
        'title',
        'tagline',
        'description',
        'long_description',
        'category',
        'role',
        'timeline',
        'client',
        'image',
        'tags',
        'metrics',
        'highlights',
        'tech_stack',
        'demo_url',
        'github_url',
        'featured',
        'color_gradient',
        'accent_color',
        'sort_order',
    ];

    protected $casts = [
        'tags' => 'array',
        'metrics' => 'array',
        'highlights' => 'array',
        'tech_stack' => 'array',
        'featured' => 'boolean',
    ];

    /**
     * Convert to the camelCase shape used by the front-end (Alpine) modal templates,
     * mirroring the original TypeScript `Project` interface.
     */
    public function toJs(): array
    {
        return [
            'id' => $this->project_key,
            'title' => $this->title,
            'tagline' => $this->tagline,
            'description' => $this->description,
            'longDescription' => $this->long_description,
            'category' => $this->category,
            'role' => $this->role,
            'timeline' => $this->timeline,
            'client' => $this->client,
            'image' => $this->image,
            'tags' => $this->tags,
            'metrics' => $this->metrics,
            'highlights' => $this->highlights,
            'techStack' => $this->tech_stack,
            'demoUrl' => $this->demo_url,
            'githubUrl' => $this->github_url,
            'featured' => $this->featured,
            'colorGradient' => $this->color_gradient,
            'accentColor' => $this->accent_color,
        ];
    }
}
