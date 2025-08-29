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
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->string('avatar_url', 500)->nullable();
                $table->enum('role', ['owner', 'admin', 'manager', 'member', 'viewer'])->default('member');
                $table->enum('status', ['active', 'inactive', 'pending'])->default('pending');
                $table->json('permissions')->nullable();
                $table->json('preferences')->nullable();
                $table->timestamp('last_login_at')->nullable();
                $table->rememberToken();
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

                // Indexes
                $table->index(['role'], 'idx_users_role');
                $table->index(['status'], 'idx_users_status');
                $table->index(['email', 'status'], 'idx_users_email_status');
                $table->index(['last_login_at'], 'idx_users_last_login');
                $table->index(['created_at', 'role'], 'idx_users_created_role');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
