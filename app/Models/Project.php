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
        'hidden_blocks',
    ];

    protected $casts = [
        'tags' => 'array',
        'metrics' => 'array',
        'highlights' => 'array',
        'tech_stack' => 'array',
        'featured' => 'boolean',
        'hidden_blocks' => 'array',
    ];

    /**
     * Iterasi 28 (Fase 5) — helper dipakai projects/show.blade.php di tiap
     * blok opsional (`!$project->isBlockHidden('metrics')`, dst) supaya
     * Blade tidak perlu menulis `!in_array(..., $project->hidden_blocks ??
     * [])` berulang 7x. `hidden_blocks` NULL (belum pernah diatur admin) =
     * tidak ada yang tersembunyi.
     */
    public function isBlockHidden(string $key): bool
    {
        return in_array($key, $this->hidden_blocks ?? [], true);
    }

    // NOTE (Iterasi 11 / Fase 2): route-model-binding for the PUBLIC detail
    // page uses project_key, not id — done via the explicit `{project:
    // project_key}` syntax in routes/web.php, NOT via a getRouteKeyName()
    // override here. An override would apply globally, which would break
    // every ADMIN route (routes/admin.php uses plain `{project}` bound by
    // numeric id, e.g. PUT /admin/projects/{project}) since implicit
    // binding falls back to getRouteKeyName() too. Route-scoped binding
    // keeps the two id schemes independent.

    // NOTE (Iterasi 11 / Fase 2): the `toJs()` camelCase-shape helper that
    // used to live here was removed — it only existed to @js()-embed a
    // Project into the on-page case-study modal (project-modal.blade.php),
    // which was retired in this iteration in favour of a real page
    // (resources/views/projects/show.blade.php, server-rendered directly
    // from the model). BlogPost::toJs() is unrelated and still used by the
    // article reader modal, which stays out of Fase 2 scope.
}
