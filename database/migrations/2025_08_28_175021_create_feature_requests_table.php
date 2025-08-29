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
        if (!Schema::hasTable('feature_requests')) {
            Schema::create('feature_requests', function (Blueprint $table) {
                $table->id();
                $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
                $table->string('title', 500);
                $table->text('description')->nullable();
                $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
                $table->enum('status', ['idea', 'planned', 'in_progress', 'completed', 'cancelled'])->default('idea');
                $table->enum('type', ['feature', 'enhancement', 'bug_fix', 'technical'])->default('feature');
                $table->enum('effort_estimate', ['xs', 's', 'm', 'l', 'xl'])->nullable();
                $table->enum('business_value', ['low', 'medium', 'high', 'critical'])->default('medium');
                $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');
                $table->foreignId('roadmap_id')->nullable()->constrained('roadmaps')->onDelete('set null');
                $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
                $table->date('due_date')->nullable();
                $table->date('completion_date')->nullable();
                $table->integer('votes_count')->default(0);
                $table->integer('comments_count')->default(0);
                $table->json('attachments')->nullable();
                $table->json('custom_fields')->nullable();
                $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

                // Indexes
                $table->index(['project_id'], 'idx_feature_requests_project');
                $table->index(['status'], 'idx_feature_requests_status');
                $table->index(['priority'], 'idx_feature_requests_priority');
                $table->index(['category_id'], 'idx_feature_requests_category');
                $table->index(['roadmap_id'], 'idx_feature_requests_roadmap');
                $table->index(['assigned_to'], 'idx_feature_requests_assigned_to');
                $table->index(['created_by'], 'idx_feature_requests_created_by');
                $table->index(['votes_count'], 'idx_feature_requests_votes');
                $table->index(['due_date'], 'idx_feature_requests_due_date');
                $table->index(['project_id', 'status'], 'idx_feature_requests_project_status');
                $table->index(['assigned_to', 'status'], 'idx_feature_requests_assigned_status');
                $table->index(['created_by', 'status'], 'idx_feature_requests_created_by_status');
                $table->index(['priority', 'created_at'], 'idx_feature_requests_priority_created');
                $table->index(['type', 'status'], 'idx_feature_requests_type_status');
                $table->index(['status', 'priority'], 'idx_feature_requests_status_priority');
                $table->index(['status', 'priority', 'created_at'], 'idx_dashboard_features');
                $table->index(['assigned_to', 'status', 'due_date'], 'idx_assigned_features');
                $table->index(['project_id', 'status', 'votes_count'], 'idx_project_features');

                // Full-text search index
                $table->fullText(['title', 'description'], 'idx_feature_requests_search');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feature_requests');
    }
};
