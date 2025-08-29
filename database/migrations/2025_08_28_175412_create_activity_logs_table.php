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
        if (!Schema::hasTable('activity_logs')) {
            Schema::create('activity_logs', function (Blueprint $table) {
                $table->id();
                $table->string('subject_type', 100);
                $table->unsignedBigInteger('subject_id');
                $table->string('action', 100);
                $table->text('description')->nullable();
                $table->json('old_values')->nullable();
                $table->json('new_values')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamp('created_at')->useCurrent();

                // Indexes
                $table->index(['subject_type', 'subject_id'], 'idx_activity_subject');
                $table->index(['action'], 'idx_activity_action');
                $table->index(['created_by'], 'idx_activity_created_by');
                $table->index(['created_at'], 'idx_activity_created_at');
                $table->index(['subject_type', 'subject_id', 'action'], 'idx_activity_subject_action');
                $table->index(['created_by', 'action', 'created_at'], 'idx_activity_created_by_action');
                $table->index(['subject_type', 'created_at'], 'idx_activity_timeline');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
