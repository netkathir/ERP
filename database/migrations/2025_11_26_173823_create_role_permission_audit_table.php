<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRolePermissionAuditTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('role_permission_audit', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->nullable()->constrained('roles')->onDelete('set null');
            $table->foreignId('permission_id')->nullable()->constrained('permissions')->onDelete('set null');
            $table->foreignId('changed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('action'); // 'created', 'updated', 'deleted', 'permission_assigned', 'permission_removed'
            $table->string('field_name')->nullable(); // 'read', 'write', 'delete', 'name', 'description'
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->text('description')->nullable(); // Additional context
            $table->timestamps();
            
            $table->index(['role_id', 'created_at']);
            $table->index(['permission_id', 'created_at']);
            $table->index('changed_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('role_permission_audit');
    }
}
