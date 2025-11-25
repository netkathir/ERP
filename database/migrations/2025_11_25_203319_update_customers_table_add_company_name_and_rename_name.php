<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCustomersTableAddCompanyNameAndRenameName extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            // Add company_name column (required) after id
            $table->string('company_name')->after('id');
        });
        
        // Use raw SQL to rename column (more reliable across database drivers)
        \DB::statement('ALTER TABLE customers CHANGE name contact_name VARCHAR(255) NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Reverse: rename contact_name back to name and make it required
        \DB::statement('ALTER TABLE customers CHANGE contact_name name VARCHAR(255) NOT NULL');
        
        Schema::table('customers', function (Blueprint $table) {
            // Drop company_name column
            $table->dropColumn('company_name');
        });
    }
}
