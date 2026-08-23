<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_posts', function (Blueprint $table) {
            $table->id();
            $table->string('post_key')->unique(); // original string id e.g. 'post-1'
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('summary');
            $table->string('category'); // Frontend Architecture | Performance | Clean Code | Full-Stack | Career & Insights
            $table->json('tags');
            $table->string('read_time');
            $table->string('published_at');
            $table->string('cover_image');
            $table->string('author_name');
            $table->string('author_role');
            $table->string('author_avatar');
            $table->unsignedInteger('likes')->default(0);
            $table->string('views')->default('0');
            $table->json('sections'); // ArticleSection[]
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_posts');
    }
};
