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
        if (!Schema::hasTable('votes')) {
            Schema::create('votes', function (Blueprint $table) {
                $table->id();
                $table->enum('votable_type', ['feature', 'feedbacks']);
                $table->unsignedBigInteger('votable_id');
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
                $table->enum('vote_type', ['up', 'down']);
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

                // Unique constraint to prevent duplicate votes
                $table->unique(['votable_type', 'votable_id', 'user_id', 'ip_address'], 'votes_unique_vote');

                // Indexes
                $table->index(['votable_type', 'votable_id'], 'idx_votes_votable');
                $table->index(['user_id'], 'idx_votes_user');
                $table->index(['votable_type', 'votable_id', 'user_id'], 'idx_votes_votable_user');
                $table->index(['user_id', 'created_at'], 'idx_votes_user_created');
                $table->index(['votable_type', 'votable_id', 'user_id', 'created_at'], 'idx_votes_votable_user_created');
                $table->index(['user_id', 'votable_type', 'votable_id'], 'idx_votes_user_votable');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('votes');
    }
};
