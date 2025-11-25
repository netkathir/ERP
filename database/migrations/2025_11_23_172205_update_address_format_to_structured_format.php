<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateAddressFormatToStructuredFormat extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Update branches table
        if (Schema::hasColumn('branches', 'address')) {
            Schema::table('branches', function (Blueprint $table) {
                $table->dropColumn('address');
            });
        }
        
        Schema::table('branches', function (Blueprint $table) {
            $table->string('address_line_1')->nullable()->after('description');
            $table->string('address_line_2')->nullable()->after('address_line_1');
            $table->string('city')->nullable()->after('address_line_2');
            $table->string('state')->nullable()->after('city');
            $table->string('pincode')->nullable()->after('state');
        });

        // Update customers table - billing_address
        if (Schema::hasColumn('customers', 'billing_address')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->dropColumn('billing_address');
            });
        }
        
        Schema::table('customers', function (Blueprint $table) {
            $table->string('billing_address_line_1')->nullable()->after('gst_no');
            $table->string('billing_address_line_2')->nullable()->after('billing_address_line_1');
            $table->string('billing_city')->nullable()->after('billing_address_line_2');
            $table->string('billing_state')->nullable()->after('billing_city');
            $table->string('billing_pincode')->nullable()->after('billing_state');
        });

        // Update customers table - shipping_address
        if (Schema::hasColumn('customers', 'shipping_address')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->dropColumn('shipping_address');
            });
        }
        
        Schema::table('customers', function (Blueprint $table) {
            $table->string('shipping_address_line_1')->nullable()->after('billing_pincode');
            $table->string('shipping_address_line_2')->nullable()->after('shipping_address_line_1');
            $table->string('shipping_city')->nullable()->after('shipping_address_line_2');
            $table->string('shipping_state')->nullable()->after('shipping_city');
            $table->string('shipping_pincode')->nullable()->after('shipping_state');
        });

        // Update quotations table
        if (Schema::hasColumn('quotations', 'billing_address')) {
            Schema::table('quotations', function (Blueprint $table) {
                $table->dropColumn('billing_address');
            });
        }
        
        Schema::table('quotations', function (Blueprint $table) {
            $table->string('billing_address_line_1')->nullable()->after('customer_id');
            $table->string('billing_address_line_2')->nullable()->after('billing_address_line_1');
            $table->string('billing_city')->nullable()->after('billing_address_line_2');
            $table->string('billing_state')->nullable()->after('billing_city');
            $table->string('billing_pincode')->nullable()->after('billing_state');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Revert branches table
        Schema::table('branches', function (Blueprint $table) {
            $table->dropColumn(['address_line_1', 'address_line_2', 'city', 'state', 'pincode']);
            $table->string('address')->nullable();
        });

        // Revert customers table
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn([
                'billing_address_line_1', 'billing_address_line_2', 'billing_city', 'billing_state', 'billing_pincode',
                'shipping_address_line_1', 'shipping_address_line_2', 'shipping_city', 'shipping_state', 'shipping_pincode'
            ]);
            $table->text('billing_address')->nullable();
            $table->text('shipping_address')->nullable();
        });

        // Revert quotations table
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropColumn(['billing_address_line_1', 'billing_address_line_2', 'billing_city', 'billing_state', 'billing_pincode']);
            $table->text('billing_address')->nullable();
        });
    }
}
