<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTaxTypeAndIgstFieldsToPurchaseOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_orders', 'tax_type')) {
                $table->enum('tax_type', ['cgst_sgst', 'igst'])->default('cgst_sgst')->after('net_amount');
            }
            if (!Schema::hasColumn('purchase_orders', 'gst_percent')) {
                $table->decimal('gst_percent', 5, 2)->default(0)->after('tax_type');
            }
            if (!Schema::hasColumn('purchase_orders', 'cgst_percent')) {
                $table->decimal('cgst_percent', 5, 2)->default(0)->after('gst_percent');
            }
            if (!Schema::hasColumn('purchase_orders', 'cgst_amount')) {
                $table->decimal('cgst_amount', 15, 2)->default(0)->after('cgst_percent');
            }
            if (!Schema::hasColumn('purchase_orders', 'igst_percent')) {
                $table->decimal('igst_percent', 5, 2)->default(0)->after('cgst_amount');
            }
            if (!Schema::hasColumn('purchase_orders', 'igst_amount')) {
                $table->decimal('igst_amount', 15, 2)->default(0)->after('igst_percent');
            }
            if (!Schema::hasColumn('purchase_orders', 'discount_percent')) {
                $table->decimal('discount_percent', 5, 2)->default(0)->after('igst_amount');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            if (Schema::hasColumn('purchase_orders', 'tax_type')) {
                $table->dropColumn('tax_type');
            }
            if (Schema::hasColumn('purchase_orders', 'gst_percent')) {
                $table->dropColumn('gst_percent');
            }
            if (Schema::hasColumn('purchase_orders', 'cgst_percent')) {
                $table->dropColumn('cgst_percent');
            }
            if (Schema::hasColumn('purchase_orders', 'cgst_amount')) {
                $table->dropColumn('cgst_amount');
            }
            if (Schema::hasColumn('purchase_orders', 'igst_percent')) {
                $table->dropColumn('igst_percent');
            }
            if (Schema::hasColumn('purchase_orders', 'igst_amount')) {
                $table->dropColumn('igst_amount');
            }
            if (Schema::hasColumn('purchase_orders', 'discount_percent')) {
                $table->dropColumn('discount_percent');
            }
        });
    }
}
