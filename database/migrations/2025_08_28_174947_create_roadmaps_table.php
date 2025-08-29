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
        if (!Schema::hasTable('roadmaps')) {
            Schema::create('roadmaps', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->enum('visibility', ['public', 'private', 'team'])->default('private');
                $table->enum('status', ['draft', 'active', 'archived'])->default('draft');
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();
                $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

                // Indexes
                $table->index(['created_by'], 'idx_roadmaps_created_by');
                $table->index(['status'], 'idx_roadmaps_status');
                $table->index(['status', 'start_date', 'end_date'], 'idx_roadmaps_status_dates');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roadmaps');
    }
};
