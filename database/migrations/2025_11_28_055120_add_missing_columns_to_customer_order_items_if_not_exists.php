<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddMissingColumnsToCustomerOrderItemsIfNotExists extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('customer_order_items', 'description')) {
                $table->text('description')->nullable()->after('ordered_qty');
            }
            if (!Schema::hasColumn('customer_order_items', 'pl_code')) {
                $table->string('pl_code')->nullable()->after('description');
            }
            if (!Schema::hasColumn('customer_order_items', 'unit_price')) {
                $table->decimal('unit_price', 15, 4)->default(0)->after('pl_code');
            }
            if (!Schema::hasColumn('customer_order_items', 'installation_charges')) {
                $table->decimal('installation_charges', 15, 2)->default(0)->after('unit_price');
            }
            if (!Schema::hasColumn('customer_order_items', 'line_amount')) {
                $table->decimal('line_amount', 15, 2)->default(0)->after('installation_charges');
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
        Schema::table('customer_order_items', function (Blueprint $table) {
            if (Schema::hasColumn('customer_order_items', 'description')) {
                $table->dropColumn('description');
            }
            if (Schema::hasColumn('customer_order_items', 'pl_code')) {
                $table->dropColumn('pl_code');
            }
            if (Schema::hasColumn('customer_order_items', 'unit_price')) {
                $table->dropColumn('unit_price');
            }
            if (Schema::hasColumn('customer_order_items', 'installation_charges')) {
                $table->dropColumn('installation_charges');
            }
            if (Schema::hasColumn('customer_order_items', 'line_amount')) {
                $table->dropColumn('line_amount');
            }
        });
    }
}
