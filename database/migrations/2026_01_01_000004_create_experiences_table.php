<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('experiences', function (Blueprint $table) {
            $table->id();
            $table->string('experience_key')->unique(); // original string id e.g. 'exp-1'
            $table->string('period');
            $table->string('role');
            $table->string('company');
            $table->string('location');
            $table->string('type'); // Full-Time | Contract | Remote
            $table->text('description');
            $table->json('achievements');
            $table->json('skills');
            $table->boolean('featured')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('experiences');
    }
};
