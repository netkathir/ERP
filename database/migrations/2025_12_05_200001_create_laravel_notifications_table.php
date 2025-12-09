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
        // Check if Laravel's notifications table structure exists
        if (Schema::hasTable('notifications')) {
            // Check if it's Laravel's standard structure
            if (!Schema::hasColumn('notifications', 'notifiable_type')) {
                // It's the custom table, rename it
                if (!Schema::hasTable('custom_notifications')) {
                    Schema::rename('notifications', 'custom_notifications');
                }
            } else {
                // Already Laravel's structure, skip
                return;
            }
        }
        
        // Create Laravel's standard notifications table
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('notifications') && Schema::hasColumn('notifications', 'notifiable_type')) {
            Schema::dropIfExists('notifications');
            if (Schema::hasTable('custom_notifications')) {
                Schema::rename('custom_notifications', 'notifications');
            }
        }
    }
};

