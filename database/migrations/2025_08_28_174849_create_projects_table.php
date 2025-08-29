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
        if (!Schema::hasTable('projects')) {
            Schema::create('projects', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->string('slug', 100)->unique();
                $table->string('image_url', 500)->nullable();
                $table->string('website_url', 500)->nullable();
                $table->enum('status', ['active', 'inactive', 'archived'])->default('active');
                $table->enum('visibility', ['public', 'private'])->default('private');
                $table->json('settings')->nullable();
                $table->foreignId('team_id')->nullable()->constrained('teams')->onDelete('set null');
                $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

                // Indexes
                $table->index(['team_id'], 'idx_projects_team');
                $table->index(['created_by'], 'idx_projects_created_by');
                $table->index(['status'], 'idx_projects_status');
                $table->index(['created_at', 'visibility'], 'idx_projects_created_visibility');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
