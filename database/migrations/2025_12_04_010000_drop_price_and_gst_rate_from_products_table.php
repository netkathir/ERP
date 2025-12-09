<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropPriceAndGstRateFromProductsTable extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'price')) {
                $table->dropColumn('price');
            }
            if (Schema::hasColumn('products', 'gst_rate')) {
                $table->dropColumn('gst_rate');
            }
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'price')) {
                $table->decimal('price', 10, 2)->default(0)->after('unit_id');
            }
            if (!Schema::hasColumn('products', 'gst_rate')) {
                $table->decimal('gst_rate', 5, 2)->default(0)->after('price');
            }
        });
    }
}


