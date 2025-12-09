<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPoSrNoToCustomerOrderSchedulesAndAmendments extends Migration
{
    public function up()
    {
        Schema::table('customer_order_schedules', function (Blueprint $table) {
            $table->string('po_sr_no')->nullable()->after('customer_order_item_id');
        });

        Schema::table('customer_order_amendments', function (Blueprint $table) {
            $table->string('po_sr_no')->nullable()->after('customer_order_item_id');
        });
    }

    public function down()
    {
        Schema::table('customer_order_schedules', function (Blueprint $table) {
            $table->dropColumn('po_sr_no');
        });

        Schema::table('customer_order_amendments', function (Blueprint $table) {
            $table->dropColumn('po_sr_no');
        });
    }
}


