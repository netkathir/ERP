<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // purchase_indent, customer_order, tender, etc.
            $table->string('sender')->default('Admin');
            $table->text('message');
            $table->string('action_type')->nullable(); // check, view, etc.
            $table->string('action_url')->nullable(); // URL to redirect when action clicked
            $table->foreignId('related_id')->nullable(); // ID of related record (purchase_indent_id, etc.)
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // User who should see this notification
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};

