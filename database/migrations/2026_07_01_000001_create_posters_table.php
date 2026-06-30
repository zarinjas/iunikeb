<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cooperative_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('alt_text')->nullable();
            $table->string('image_path');
            $table->string('link_url')->nullable();
            $table->string('type')->default('poster');
            $table->string('audience')->default('members');
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['cooperative_id', 'type', 'audience', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posters');
    }
};
