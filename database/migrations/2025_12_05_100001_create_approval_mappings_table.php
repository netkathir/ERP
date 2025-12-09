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
        Schema::create('approval_mappings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('approval_master_id')->constrained('approval_masters')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->integer('approval_order')->default(1)->comment('Order in which approval should be processed (for sequential approvals)');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Ensure a user can only be mapped once per form
            $table->unique(['approval_master_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approval_mappings');
    }
};

