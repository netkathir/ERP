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
        // Add branch_id to customers table
        if (Schema::hasTable('customers') && !Schema::hasColumn('customers', 'branch_id')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->foreignId('branch_id')->nullable()->after('contact_info')->constrained('branches')->onDelete('cascade');
            });
        }

        // Add branch_id to products table
        if (Schema::hasTable('products') && !Schema::hasColumn('products', 'branch_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->foreignId('branch_id')->nullable()->after('category')->constrained('branches')->onDelete('cascade');
            });
        }

        // Add branch_id to units table (optional - units might be shared)
        if (Schema::hasTable('units') && !Schema::hasColumn('units', 'branch_id')) {
            Schema::table('units', function (Blueprint $table) {
                $table->foreignId('branch_id')->nullable()->after('symbol')->constrained('branches')->onDelete('cascade');
            });
        }

        // Add branch_id to quotations table
        if (Schema::hasTable('quotations') && !Schema::hasColumn('quotations', 'branch_id')) {
            Schema::table('quotations', function (Blueprint $table) {
                $table->foreignId('branch_id')->nullable()->after('status')->constrained('branches')->onDelete('cascade');
            });
        }

        // Add branch_id to proforma_invoices table
        if (Schema::hasTable('proforma_invoices') && !Schema::hasColumn('proforma_invoices', 'branch_id')) {
            Schema::table('proforma_invoices', function (Blueprint $table) {
                $table->foreignId('branch_id')->nullable()->after('status')->constrained('branches')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('customers', 'branch_id')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->dropForeign(['branch_id']);
                $table->dropColumn('branch_id');
            });
        }

        if (Schema::hasColumn('products', 'branch_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropForeign(['branch_id']);
                $table->dropColumn('branch_id');
            });
        }

        if (Schema::hasColumn('units', 'branch_id')) {
            Schema::table('units', function (Blueprint $table) {
                $table->dropForeign(['branch_id']);
                $table->dropColumn('branch_id');
            });
        }

        if (Schema::hasColumn('quotations', 'branch_id')) {
            Schema::table('quotations', function (Blueprint $table) {
                $table->dropForeign(['branch_id']);
                $table->dropColumn('branch_id');
            });
        }

        if (Schema::hasColumn('proforma_invoices', 'branch_id')) {
            Schema::table('proforma_invoices', function (Blueprint $table) {
                $table->dropForeign(['branch_id']);
                $table->dropColumn('branch_id');
            });
        }
    }
};
