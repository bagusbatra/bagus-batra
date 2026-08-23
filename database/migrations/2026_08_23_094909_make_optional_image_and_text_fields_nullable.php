<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Iterasi 9 (Polish & QA) fix, same root cause as the sibling migration
     * `..._094419_make_color_gradient_and_accent_color_nullable_on_projects_table`
     * found a few minutes earlier in the same regression pass: several
     * columns were created NOT NULL with no default back when the schema
     * was first generated from seed data (every seeded row already had a
     * value for every field, so nobody noticed), but the admin CRUD forms
     * added in Iterasi 3/4/6/7 all treat these same fields as optional —
     * 'nullable' validation rule, no `required` HTML attribute, and (for
     * the image fields) a resolveImage() helper that falls back to `null`
     * when creating a new row with neither an uploaded file nor a URL
     * typed in. Leaving any of these blank on create crashed with a 500
     * SQL error. Found by systematically cross-checking every NOT NULL
     * string/text column against its controller's validation rules after
     * `projects.color_gradient` surfaced the pattern during CRUD regression
     * testing.
     *
     * Affected columns (all confirmed unused-if-null by the public Blade
     * views they feed, or already handled with an `?: 'fallback'` /
     * `x-show` guard, so making them nullable is purely corrective, not a
     * behavior change for the 5/4/3/12 existing seeded rows which already
     * have non-null values):
     *   - projects.image            (ProjectController::resolveImage)
     *   - blog_posts.cover_image    (BlogPostController::resolveImage)
     *   - blog_posts.author_avatar  (BlogPostController::resolveImage)
     *   - testimonials.avatar       (TestimonialController::resolveImage)
     *   - testimonials.project_tag  (validation already says 'nullable')
     *   - skills.highlight_text     (validation already says 'nullable')
     */
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }

        DB::statement('ALTER TABLE `projects` MODIFY `image` VARCHAR(255) NULL');
        DB::statement('ALTER TABLE `blog_posts` MODIFY `cover_image` VARCHAR(255) NULL');
        DB::statement('ALTER TABLE `blog_posts` MODIFY `author_avatar` VARCHAR(255) NULL');
        DB::statement('ALTER TABLE `testimonials` MODIFY `avatar` VARCHAR(255) NULL');
        DB::statement('ALTER TABLE `testimonials` MODIFY `project_tag` VARCHAR(100) NULL');
        DB::statement('ALTER TABLE `skills` MODIFY `highlight_text` VARCHAR(255) NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("ALTER TABLE `projects` MODIFY `image` VARCHAR(255) NOT NULL DEFAULT ''");
        DB::statement("ALTER TABLE `blog_posts` MODIFY `cover_image` VARCHAR(255) NOT NULL DEFAULT ''");
        DB::statement("ALTER TABLE `blog_posts` MODIFY `author_avatar` VARCHAR(255) NOT NULL DEFAULT ''");
        DB::statement("ALTER TABLE `testimonials` MODIFY `avatar` VARCHAR(255) NOT NULL DEFAULT ''");
        DB::statement("ALTER TABLE `testimonials` MODIFY `project_tag` VARCHAR(100) NOT NULL DEFAULT ''");
        DB::statement("ALTER TABLE `skills` MODIFY `highlight_text` VARCHAR(255) NOT NULL DEFAULT ''");
    }
};
