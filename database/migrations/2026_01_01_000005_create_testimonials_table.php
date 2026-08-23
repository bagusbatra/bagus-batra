<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('testimonial_key')->unique(); // original string id e.g. 'test-1'
            $table->string('name');
            $table->string('role');
            $table->string('company');
            $table->string('avatar');
            $table->text('content');
            $table->unsignedTinyInteger('rating');
            $table->string('project_tag');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('testimonials');
    }
};
