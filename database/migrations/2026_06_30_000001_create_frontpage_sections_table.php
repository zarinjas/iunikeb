<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Clean up old CMS tables
        Schema::dropIfExists('page_sections');
        Schema::dropIfExists('pages');
        Schema::dropIfExists('news');
        Schema::dropIfExists('announcement_member');
        Schema::dropIfExists('announcements');
        Schema::dropIfExists('banners');
        Schema::dropIfExists('popups');
        Schema::dropIfExists('posters');
        Schema::dropIfExists('document_categories');
        Schema::dropIfExists('documents');

        // Frontpage sections
        Schema::create('frontpage_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cooperative_id')->constrained()->cascadeOnDelete();
            $table->string('key');
            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->json('data')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['cooperative_id', 'key']);
        });

        // Frontpage section items (repeatable content: slides, cards, stats, etc.)
        Schema::create('frontpage_section_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained('frontpage_sections')->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->string('icon')->nullable();
            $table->string('value')->nullable();
            $table->string('button_text')->nullable();
            $table->string('button_url')->nullable();
            $table->json('extra')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Dynamic navigation menus
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cooperative_id')->constrained()->cascadeOnDelete();
            $table->string('location'); // header, footer_col1, footer_col2, etc.
            $table->string('label');
            $table->string('url')->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('menus')->cascadeOnDelete();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menus');
        Schema::dropIfExists('frontpage_section_items');
        Schema::dropIfExists('frontpage_sections');
    }
};
