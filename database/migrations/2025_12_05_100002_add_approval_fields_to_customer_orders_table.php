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
        Schema::table('customer_orders', function (Blueprint $table) {
            $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('pending')->after('net_amount');
            $table->foreignId('approved_by')->nullable()->after('approval_status')->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable()->after('approved_by');
            $table->text('approval_remarks')->nullable()->after('approved_at');
            $table->foreignId('rejected_by')->nullable()->after('approval_remarks')->constrained('users')->onDelete('set null');
            $table->timestamp('rejected_at')->nullable()->after('rejected_by');
            $table->text('rejection_remarks')->nullable()->after('rejected_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_orders', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropForeign(['rejected_by']);
            $table->dropColumn([
                'approval_status',
                'approved_by',
                'approved_at',
                'approval_remarks',
                'rejected_by',
                'rejected_at',
                'rejection_remarks'
            ]);
        });
    }
};

