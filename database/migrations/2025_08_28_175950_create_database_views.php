<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create feature_stats_view
        DB::statement("
            CREATE OR REPLACE VIEW feature_stats_view AS
            SELECT
                f.project_id AS project_id,
                count(0) AS total_features,
                count(case when f.status = 'idea' then 1 end) AS ideas_count,
                count(case when f.status = 'planned' then 1 end) AS planned_count,
                count(case when f.status = 'in_progress' then 1 end) AS in_progress_count,
                count(case when f.status = 'completed' then 1 end) AS completed_count,
                count(case when f.status = 'cancelled' then 1 end) AS cancelled_count,
                count(case when f.priority = 'critical' then 1 end) AS critical_priority_count,
                count(case when f.priority = 'high' then 1 end) AS high_priority_count,
                avg(f.votes_count) AS avg_votes,
                max(f.votes_count) AS max_votes,
                sum(f.votes_count) AS total_votes
            FROM feature_requests AS f
            GROUP BY f.project_id
        ");

        // Create feedback_stats_view
        DB::statement("
            CREATE OR REPLACE VIEW feedback_stats_view AS
            SELECT
                fb.project_id AS project_id,
                count(0) AS total_feedback,
                count(case when fb.status = 'open' then 1 end) AS open_count,
                count(case when fb.status = 'under_review' then 1 end) AS under_review_count,
                count(case when fb.status = 'planned' then 1 end) AS planned_count,
                count(case when fb.status = 'in_progress' then 1 end) AS in_progress_count,
                count(case when fb.status = 'completed' then 1 end) AS completed_count,
                count(case when fb.status = 'rejected' then 1 end) AS rejected_count,
                count(case when fb.type = 'feature_request' then 1 end) AS feature_requests_count,
                count(case when fb.type = 'bug_report' then 1 end) AS bug_reports_count,
                count(case when fb.type = 'improvement' then 1 end) AS improvements_count,
                avg(fb.upvotes_count) AS avg_upvotes,
                max(fb.upvotes_count) AS max_upvotes,
                sum(fb.upvotes_count) AS total_upvotes
            FROM feedbacks AS fb
            GROUP BY fb.project_id
        ");

        // Create application_summary view
        DB::statement("
            CREATE OR REPLACE VIEW application_summary AS
            SELECT
                count(distinct u.id) AS total_users,
                count(distinct p.id) AS total_projects,
                coalesce(sum(fs.total_features),0) AS total_features,
                coalesce(sum(fbs.total_feedback),0) AS total_feedback,
                coalesce(sum(fs.total_votes),0) AS total_feature_votes,
                coalesce(sum(fbs.total_upvotes),0) AS total_feedback_votes,
                (SELECT COUNT(*) FROM users WHERE status = 'active') AS active_users,
                (SELECT COUNT(*) FROM projects WHERE status = 'active') AS active_projects,
                (SELECT COUNT(*) FROM feature_requests WHERE status = 'in_progress') AS features_in_progress,
                (SELECT COUNT(*) FROM feedbacks WHERE status = 'open') AS open_feedback
            FROM users u
            LEFT JOIN projects p ON p.status = 'active'
            LEFT JOIN feature_stats_view fs ON p.id = fs.project_id
            LEFT JOIN feedback_stats_view fbs ON p.id = fbs.project_id
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS application_summary');
        DB::statement('DROP VIEW IF EXISTS feedback_stats_view');
        DB::statement('DROP VIEW IF EXISTS feature_stats_view');
    }
};
