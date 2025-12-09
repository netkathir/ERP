<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddReadWriteDeleteToRolePermissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Check if columns exist
        $columns = DB::select("SHOW COLUMNS FROM role_permission");
        $columnNames = array_column($columns, 'Field');
        
        if (!in_array('read', $columnNames)) {
            Schema::table('role_permission', function (Blueprint $table) {
                $table->boolean('read')->default(false)->after('permission_id');
            });
        }
        
        if (!in_array('write', $columnNames)) {
            Schema::table('role_permission', function (Blueprint $table) {
                $table->boolean('write')->default(false)->after('read');
            });
        }
        
        if (!in_array('delete', $columnNames)) {
            Schema::table('role_permission', function (Blueprint $table) {
                $table->boolean('delete')->default(false)->after('write');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('role_permission', function (Blueprint $table) {
            $table->dropColumn(['read', 'write', 'delete']);
        });
    }
}
