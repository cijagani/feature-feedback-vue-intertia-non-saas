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
        if (!Schema::hasTable('feature_tags')) {
            Schema::create('feature_tags', function (Blueprint $table) {
                $table->id();
                $table->foreignId('feature_id')->constrained('feature_requests')->onDelete('cascade');
                $table->foreignId('tag_id')->constrained('tags')->onDelete('cascade');
                $table->timestamp('created_at')->useCurrent();

                // Unique constraint
                $table->unique(['feature_id', 'tag_id'], 'feature_tags_feature_tag_unique');

                // Indexes
                $table->index(['tag_id'], 'idx_feature_tags_tag');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feature_tags');
    }
};
