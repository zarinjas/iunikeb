<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('announcements', function (Blueprint $table): void {
            $table->string('priority')->default('normal')->after('is_pinned');
            $table->string('send_via')->default('in_app')->after('expires_at');
            $table->index(['cooperative_id', 'priority']);
        });
    }

    public function down(): void
    {
        Schema::table('announcements', function (Blueprint $table): void {
            $table->dropIndex(['cooperative_id', 'priority']);
            $table->dropColumn(['priority', 'send_via']);
        });
    }
};
