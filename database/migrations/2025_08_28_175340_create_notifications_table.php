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
        if (!Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->string('type', 100);
                $table->string('title');
                $table->text('message')->nullable();
                $table->json('data')->nullable();
                $table->timestamp('read_at')->nullable();
                $table->timestamp('created_at')->useCurrent();

                // Indexes
                $table->index(['user_id'], 'idx_notifications_user');
                $table->index(['read_at'], 'idx_notifications_read');
                $table->index(['created_at'], 'idx_notifications_created_at');
                $table->index(['user_id', 'read_at'], 'idx_notifications_user_read');
                $table->index(['type', 'created_at'], 'idx_notifications_type_created');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
