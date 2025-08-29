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
        if (!Schema::hasTable('feedbacks')) {
            Schema::create('feedbacks', function (Blueprint $table) {
                $table->id();
                $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
                $table->string('title', 500);
                $table->text('description')->nullable();
                $table->enum('type', ['feature_request', 'bug_report', 'improvement', 'question'])->default('feature_request');
                $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
                $table->enum('status', ['open', 'under_review', 'planned', 'in_progress', 'completed', 'rejected'])->default('open');
                $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');
                $table->integer('upvotes_count')->default(0);
                $table->integer('downvotes_count')->default(0);
                $table->integer('comments_count')->default(0);
                $table->json('attachments')->nullable();
                $table->json('metadata')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
                $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamp('resolved_at')->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

                // Indexes
                $table->index(['project_id'], 'idx_feedbacks_project');
                $table->index(['status'], 'idx_feedbacks_status');
                $table->index(['type'], 'idx_feedbacks_type');
                $table->index(['category_id'], 'idx_feedbacks_category');
                $table->index(['created_by'], 'idx_feedbacks_created_by');
                $table->index(['assigned_to'], 'idx_feedbacks_assigned_to');
                $table->index(['upvotes_count'], 'idx_feedbacks_upvotes');
                $table->index(['resolved_at'], 'idx_feedbacks_resolved_at');
                $table->index(['project_id', 'status'], 'idx_feedbacks_project_status');
                $table->index(['assigned_to', 'status'], 'idx_feedbacks_assigned_status');
                $table->index(['created_by', 'status'], 'idx_feedbacks_created_by_status');
                $table->index(['priority', 'created_at'], 'idx_feedbacks_priority_created');
                $table->index(['type', 'status'], 'idx_feedbacks_type_status');
                $table->index(['status', 'type', 'created_at'], 'idx_dashboard_feedback');
                $table->index(['assigned_to', 'status', 'priority'], 'idx_assigned_feedback');
                $table->index(['project_id', 'type', 'upvotes_count'], 'idx_project_feedback');

                // Full-text search index
                $table->fullText(['title', 'description'], 'idx_feedbacks_search');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedbacks');
    }
};
