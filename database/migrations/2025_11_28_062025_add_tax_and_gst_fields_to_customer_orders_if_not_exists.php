<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTaxAndGstFieldsToCustomerOrdersIfNotExists extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_orders', function (Blueprint $table) {
            // Tax / amount summary fields
            if (!Schema::hasColumn('customer_orders', 'tax_type')) {
                $table->enum('tax_type', ['cgst_sgst', 'igst'])->default('cgst_sgst');
            }
            if (!Schema::hasColumn('customer_orders', 'total_amount')) {
                $table->decimal('total_amount', 15, 2)->default(0);
            }
            if (!Schema::hasColumn('customer_orders', 'gst_percent')) {
                $table->decimal('gst_percent', 5, 2)->default(0);
            }
            if (!Schema::hasColumn('customer_orders', 'cgst_percent')) {
                $table->decimal('cgst_percent', 5, 2)->default(0);
            }
            if (!Schema::hasColumn('customer_orders', 'cgst_amount')) {
                $table->decimal('cgst_amount', 15, 2)->default(0);
            }
            if (!Schema::hasColumn('customer_orders', 'sgst_percent')) {
                $table->decimal('sgst_percent', 5, 2)->default(0);
            }
            if (!Schema::hasColumn('customer_orders', 'sgst_amount')) {
                $table->decimal('sgst_amount', 15, 2)->default(0);
            }
            if (!Schema::hasColumn('customer_orders', 'igst_percent')) {
                $table->decimal('igst_percent', 5, 2)->default(0);
            }
            if (!Schema::hasColumn('customer_orders', 'igst_amount')) {
                $table->decimal('igst_amount', 15, 2)->default(0);
            }
            if (!Schema::hasColumn('customer_orders', 'freight')) {
                $table->decimal('freight', 15, 2)->default(0);
            }
            if (!Schema::hasColumn('customer_orders', 'inspection_charges')) {
                $table->decimal('inspection_charges', 15, 2)->default(0);
            }
            if (!Schema::hasColumn('customer_orders', 'net_amount')) {
                $table->decimal('net_amount', 15, 2)->default(0);
            }
            if (!Schema::hasColumn('customer_orders', 'amount_note')) {
                $table->text('amount_note')->nullable();
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
        Schema::table('customer_orders', function (Blueprint $table) {
            if (Schema::hasColumn('customer_orders', 'tax_type')) {
                $table->dropColumn('tax_type');
            }
            if (Schema::hasColumn('customer_orders', 'total_amount')) {
                $table->dropColumn('total_amount');
            }
            if (Schema::hasColumn('customer_orders', 'gst_percent')) {
                $table->dropColumn('gst_percent');
            }
            if (Schema::hasColumn('customer_orders', 'cgst_percent')) {
                $table->dropColumn('cgst_percent');
            }
            if (Schema::hasColumn('customer_orders', 'cgst_amount')) {
                $table->dropColumn('cgst_amount');
            }
            if (Schema::hasColumn('customer_orders', 'sgst_percent')) {
                $table->dropColumn('sgst_percent');
            }
            if (Schema::hasColumn('customer_orders', 'sgst_amount')) {
                $table->dropColumn('sgst_amount');
            }
            if (Schema::hasColumn('customer_orders', 'igst_percent')) {
                $table->dropColumn('igst_percent');
            }
            if (Schema::hasColumn('customer_orders', 'igst_amount')) {
                $table->dropColumn('igst_amount');
            }
            if (Schema::hasColumn('customer_orders', 'freight')) {
                $table->dropColumn('freight');
            }
            if (Schema::hasColumn('customer_orders', 'inspection_charges')) {
                $table->dropColumn('inspection_charges');
            }
            if (Schema::hasColumn('customer_orders', 'net_amount')) {
                $table->dropColumn('net_amount');
            }
            if (Schema::hasColumn('customer_orders', 'amount_note')) {
                $table->dropColumn('amount_note');
            }
        });
    }
}
