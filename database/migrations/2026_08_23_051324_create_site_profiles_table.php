<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_profiles', function (Blueprint $table) {
            $table->id(); // singleton — selalu 1 baris, id = 1
            $table->string('name');
            $table->string('nickname');
            $table->string('title_id');
            $table->string('title_en');
            $table->text('tagline_id');
            $table->text('tagline_en');
            $table->text('bio_id');
            $table->text('bio_en');
            $table->string('location');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('github')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('twitter')->nullable();
            $table->boolean('available_for_work')->default(true);
            $table->string('years_of_exp');
            $table->string('completed_projects');
            $table->string('client_satisfaction');
            $table->string('open_source_contributions');
            $table->string('avatar');
            $table->string('secondary_avatar')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_profiles');
    }
};
