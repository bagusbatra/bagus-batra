<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Support\RichText;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BlogPostController extends Controller
{
    /** "All" is a filter-only pseudo-category on the public page (blog.blade.php), never a real post category. */
    public const CATEGORIES = ['Frontend Architecture', 'Performance', 'Clean Code', 'Full-Stack'];

    public function index(Request $request): View
    {
        $query = BlogPost::query();

        if ($search = $request->string('search')->trim()->value()) {
            $query->where('title', 'like', "%{$search}%");
        }

        if ($category = $request->string('category')->trim()->value()) {
            $query->where('category', $category);
        }

        $posts = $query->orderBy('sort_order')->paginate(10)->withQueryString();

        return view('admin.blog.index', [
            'posts' => $posts,
            'categories' => self::CATEGORIES,
            'search' => $search ?? '',
            'categoryFilter' => $category ?? '',
        ]);
    }

    public function create(): View
    {
        return view('admin.blog.form', ['post' => new BlogPost(), 'categories' => self::CATEGORIES]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['post_key'] = $this->uniqueKey($data['title'], 'post_key');
        $data['slug'] = $this->uniqueKey($data['title'], 'slug');
        $data['sort_order'] = (int) (BlogPost::max('sort_order') ?? -1) + 1;
        $data['cover_image'] = $this->resolveImage($request, 'cover_image_file', $request->input('cover_image_url'), null);
        $data['author_avatar'] = $this->resolveImage($request, 'author_avatar_file', $request->input('author_avatar_url'), null);

        BlogPost::create($data);

        return redirect()->route('admin.blog')->with('success', 'Artikel blog baru berhasil ditambahkan.');
    }

    public function edit(BlogPost $post): View
    {
        return view('admin.blog.form', ['post' => $post, 'categories' => self::CATEGORIES]);
    }

    public function update(Request $request, BlogPost $post): RedirectResponse
    {
        // post_key & slug are intentionally immutable after creation, same
        // rationale as Project::project_key (see docs/LOG-ITERASI.md Iterasi 4).
        $data = $this->validated($request);
        $data['cover_image'] = $this->resolveImage($request, 'cover_image_file', $request->input('cover_image_url'), $post->cover_image);
        $data['author_avatar'] = $this->resolveImage($request, 'author_avatar_file', $request->input('author_avatar_url'), $post->author_avatar);

        $post->update($data);

        return redirect()->route('admin.blog')->with('success', 'Artikel blog berhasil diperbarui.');
    }

    public function destroy(BlogPost $post): RedirectResponse
    {
        $post->delete();

        return redirect()->route('admin.blog')->with('success', 'Artikel blog berhasil dihapus.');
    }

    public function move(Request $request, BlogPost $post): RedirectResponse
    {
        $direction = $request->validate([
            'direction' => ['required', 'in:up,down'],
        ])['direction'];

        $neighbour = $direction === 'up'
            ? BlogPost::where('sort_order', '<', $post->sort_order)->orderBy('sort_order', 'desc')->first()
            : BlogPost::where('sort_order', '>', $post->sort_order)->orderBy('sort_order', 'asc')->first();

        if ($neighbour) {
            [$a, $b] = [$post->sort_order, $neighbour->sort_order];
            $post->update(['sort_order' => $b]);
            $neighbour->update(['sort_order' => $a]);
        }

        return redirect()->route('admin.blog');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'summary' => ['required', 'string'],
            'category' => ['required', Rule::in(self::CATEGORIES)],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['nullable', 'string', 'max:100'],
            'read_time' => ['required', 'string', 'max:50'],
            'published_at' => ['required', 'string', 'max:50'],
            'cover_image_url' => ['nullable', 'string', 'max:2048'],
            'cover_image_file' => ['nullable', 'image', 'max:4096'],
            'author_name' => ['required', 'string', 'max:255'],
            'author_role' => ['required', 'string', 'max:255'],
            'author_avatar_url' => ['nullable', 'string', 'max:2048'],
            'author_avatar_file' => ['nullable', 'image', 'max:4096'],
            'likes' => ['nullable', 'integer', 'min:0'],
            'views' => ['nullable', 'string', 'max:20'],
            'sections' => ['nullable', 'array'],
            'sections.*.heading' => ['nullable', 'string', 'max:255'],
            'sections.*.body' => ['nullable', 'string'],
            'sections.*.tip' => ['nullable', 'string'],
            'sections.*.code_language' => ['nullable', 'string', 'max:30'],
            'sections.*.code_filename' => ['nullable', 'string', 'max:255'],
            'sections.*.code_code' => ['nullable', 'string'],
        ]);

        $data['tags'] = array_values(array_filter($data['tags'] ?? [], fn ($v) => filled($v)));
        $data['likes'] = (int) ($data['likes'] ?? 0);
        $data['views'] = $data['views'] ?? '0';

        $sections = [];
        foreach ($data['sections'] ?? [] as $row) {
            // Iterasi 27 (Fase 5): `body` sekarang HTML (Tiptap) — SANITASI
            // DULU (App\Support\RichText::sanitize()) SEBELUM dicek blank
            // ATAU disimpan. Editor kosong bisa menghasilkan markup
            // non-kosong scr string (mis. "<p></p>") walau tidak ada teks
            // sama sekali — cek blank pakai versi TANPA tag (strip_tags),
            // bukan string HTML mentahnya, supaya baris section yang
            // heading-nya kosong DAN body-nya "kosong secara visual" tetap
            // ke-skip sama seperti perilaku sebelum Iterasi 27.
            $bodyHtml = RichText::sanitize($row['body'] ?? null);
            $bodyIsBlank = trim(strip_tags($bodyHtml)) === '';

            if (blank($row['heading'] ?? null) && $bodyIsBlank) {
                continue; // skip fully-empty repeater rows
            }

            $section = [
                'heading' => $row['heading'] ?? '',
                'body' => $bodyHtml,
            ];

            if (filled($row['code_language'] ?? null) || filled($row['code_filename'] ?? null) || filled($row['code_code'] ?? null)) {
                $section['codeSnippet'] = [
                    'language' => $row['code_language'] ?? '',
                    'filename' => $row['code_filename'] ?? '',
                    'code' => $row['code_code'] ?? '',
                ];
            }

            if (filled($row['tip'] ?? null)) {
                $section['tip'] = $row['tip'];
            }

            $sections[] = $section;
        }
        $data['sections'] = $sections;

        unset($data['cover_image_url'], $data['cover_image_file'], $data['author_avatar_url'], $data['author_avatar_file']);

        return $data;
    }

    private function resolveImage(Request $request, string $fileField, ?string $url, ?string $current): ?string
    {
        if ($request->hasFile($fileField)) {
            return Storage::url($request->file($fileField)->store('blog', 'public'));
        }

        if (filled($url)) {
            return $url;
        }

        return $current;
    }

    private function uniqueKey(string $title, string $column): string
    {
        $base = Str::slug($title) ?: 'post';
        $key = $base;
        $i = 2;

        while (BlogPost::where($column, $key)->exists()) {
            $key = "{$base}-{$i}";
            $i++;
        }

        return $key;
    }
}
