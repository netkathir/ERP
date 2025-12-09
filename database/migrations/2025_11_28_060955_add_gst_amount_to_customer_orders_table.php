<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('customer_orders', 'gst_amount')) {
                if (Schema::hasColumn('customer_orders', 'gst_percent')) {
                    $table->decimal('gst_amount', 15, 2)->default(0)->after('gst_percent');
                } else {
                    $table->decimal('gst_amount', 15, 2)->default(0);
                }
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
            if (Schema::hasColumn('customer_orders', 'gst_amount')) {
                $table->dropColumn('gst_amount');
            }
        });
    }
};
