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
        if (!Schema::hasTable('tickets')) {
            Schema::create('tickets', function (Blueprint $table) {
                $table->id();
                $table->foreignId('staff_id')->nullable()->constrained('users')->onDelete('set null');
                $table->string('subject');
                $table->foreignId('department_id')->constrained('departments')->onDelete('cascade');
                $table->json('assignee_id')->nullable();
                $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
                $table->enum('status', ['open', 'answered', 'closed', 'on_hold'])->default('open');
                $table->string('ticket_id')->unique();
                $table->boolean('admin_viewed')->default(false);
                $table->boolean('user_viewed')->default(true);
                $table->json('attachments')->nullable();
                $table->text('body');
                $table->timestamp('closed_at')->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

                // Indexes
                $table->index(['department_id'], 'idx_tickets_department');
                $table->index(['status'], 'idx_tickets_status');
                $table->index(['priority'], 'idx_tickets_priority');
                $table->index(['staff_id'], 'fk_tickets_staff_id__users_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
