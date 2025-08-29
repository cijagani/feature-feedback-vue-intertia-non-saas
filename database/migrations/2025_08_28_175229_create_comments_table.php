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
        if (!Schema::hasTable('comments')) {
            Schema::create('comments', function (Blueprint $table) {
                $table->id();
                $table->enum('commentable_type', ['feature', 'feedbacks']);
                $table->unsignedBigInteger('commentable_id');
                $table->foreignId('parent_id')->nullable()->constrained('comments')->onDelete('cascade');
                $table->text('content');
                $table->boolean('is_internal')->default(false);
                $table->json('attachments')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

                // Indexes
                $table->index(['commentable_type', 'commentable_id'], 'idx_comments_commentable');
                $table->index(['parent_id'], 'idx_comments_parent');
                $table->index(['created_by'], 'idx_comments_created_by');
                $table->index(['commentable_type', 'commentable_id', 'created_at'], 'idx_comments_commentable_created');
                $table->index(['commentable_type', 'commentable_id', 'created_by'], 'idx_comments_commentable_user');
                $table->index(['created_at'], 'idx_comments_created');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
