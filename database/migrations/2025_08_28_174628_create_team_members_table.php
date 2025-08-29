<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('team_members')) {
            Schema::create('team_members', function (Blueprint $table) {
                $table->id();
                $table->foreignId('team_id')->constrained('teams')->onDelete('cascade');
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->enum('role', ['lead', 'member'])->default('member');
                $table->timestamp('joined_at')->useCurrent();

                // Unique constraint
                $table->unique(['team_id', 'user_id'], 'team_members_team_user_unique');

                // Indexes
                $table->index(['user_id'], 'idx_team_members_user');
                $table->index(['user_id', 'role'], 'idx_team_members_user_role');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('team_members');
    }
};
