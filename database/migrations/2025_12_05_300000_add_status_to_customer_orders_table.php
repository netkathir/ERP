<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('customer_orders', function (Blueprint $table) {
            // Check if status column doesn't exist, then add it
            if (!Schema::hasColumn('customer_orders', 'status')) {
                $table->string('status')->default('Pending')->after('net_amount');
            }

            // Check if updated_by_id column doesn't exist, then add it
            if (!Schema::hasColumn('customer_orders', 'updated_by_id')) {
                $table->foreignId('updated_by_id')->nullable()->after('status')->constrained('users')->onDelete('set null');
            }
        });

        // Remove old approval columns if they exist
        $columns = Schema::getColumnListing('customer_orders');
        
        if (in_array('approval_status', $columns)) {
            // Drop foreign keys first
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'customer_orders' 
                AND COLUMN_NAME IN ('approved_by', 'rejected_by')
                AND REFERENCED_TABLE_NAME IS NOT NULL
            ");
            
            foreach ($foreignKeys as $fk) {
                try {
                    DB::statement("ALTER TABLE `customer_orders` DROP FOREIGN KEY `{$fk->CONSTRAINT_NAME}`");
                } catch (\Exception $e) {
                    // Ignore if foreign key doesn't exist
                }
            }
            
            // Drop columns
            $columnsToDrop = [];
            if (in_array('approval_status', $columns)) $columnsToDrop[] = 'approval_status';
            if (in_array('approved_by', $columns)) $columnsToDrop[] = 'approved_by';
            if (in_array('approved_at', $columns)) $columnsToDrop[] = 'approved_at';
            if (in_array('approval_remarks', $columns)) $columnsToDrop[] = 'approval_remarks';
            if (in_array('rejected_by', $columns)) $columnsToDrop[] = 'rejected_by';
            if (in_array('rejected_at', $columns)) $columnsToDrop[] = 'rejected_at';
            if (in_array('rejection_remarks', $columns)) $columnsToDrop[] = 'rejection_remarks';
            
            if (!empty($columnsToDrop)) {
                Schema::table('customer_orders', function (Blueprint $table) use ($columnsToDrop) {
                    $table->dropColumn($columnsToDrop);
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_orders', function (Blueprint $table) {
            if (Schema::hasColumn('customer_orders', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('customer_orders', 'updated_by_id')) {
                $table->dropForeign(['updated_by_id']);
                $table->dropColumn('updated_by_id');
            }
        });
    }
};

