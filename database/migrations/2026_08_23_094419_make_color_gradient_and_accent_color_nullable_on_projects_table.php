<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Iterasi 9 (Polish & QA) fix: `color_gradient` and `accent_color` on
     * `projects` were created NOT NULL with no default (see
     * 2026_01_01_000002_create_projects_table.php), but the admin CRUD form
     * added in Iterasi 4 (ProjectController@validated) always treated both
     * as optional ('nullable' validation rule, no `required` attribute on
     * the form inputs). Creating/editing a project while leaving "Warna
     * Gradient Kartu" blank crashed with a 500 SQL error (`Field
     * 'color_gradient' doesn't have a default value`), found during the
     * Iterasi 9 regression pass. Neither field is actually consumed by any
     * public Blade view today (grep confirms `colorGradient`/`accentColor`
     * only reach the per-card JSON payload via Project::toJs(), unused by
     * project-modal.blade.php) so making them nullable is a safe, purely
     * corrective schema change — no data loss for the 5 existing seeded
     * projects, which already have non-null values.
     *
     * Raw SQL (not Schema::table()->change()) is used to avoid adding a
     * doctrine/dbal dependency solely for this one-off column change.
     */
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            // SQLite (e.g. local test runs) has no real column nullability
            // enforcement via ALTER MODIFY; nothing to do.
            return;
        }

        DB::statement('ALTER TABLE `projects` MODIFY `color_gradient` VARCHAR(255) NULL');
        DB::statement('ALTER TABLE `projects` MODIFY `accent_color` VARCHAR(20) NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("ALTER TABLE `projects` MODIFY `color_gradient` VARCHAR(255) NOT NULL DEFAULT ''");
        DB::statement("ALTER TABLE `projects` MODIFY `accent_color` VARCHAR(20) NOT NULL DEFAULT ''");
    }
};
