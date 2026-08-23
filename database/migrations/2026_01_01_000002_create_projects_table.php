<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('project_key')->unique(); // original string id e.g. 'lumina-saas'
            $table->string('title');
            $table->string('tagline');
            $table->text('description');
            $table->text('long_description');
            $table->string('category'); // Full-Stack | Frontend | UI/UX & Systems | Open Source | AI & Tools
            $table->string('role');
            $table->string('timeline');
            $table->string('client')->nullable();
            $table->string('image');
            $table->json('tags');
            $table->json('metrics'); // [{label, value}]
            $table->json('highlights'); // string[]
            $table->json('tech_stack'); // {frontend, backend?, database?, cloudAndDevOps?}
            $table->string('demo_url')->nullable();
            $table->string('github_url')->nullable();
            $table->boolean('featured')->default(false);
            $table->string('color_gradient');
            $table->string('accent_color');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
