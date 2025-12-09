<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        if (!Schema::hasTable('role_form_permissions')) {
            Schema::create('role_form_permissions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('role_id');
                $table->unsignedBigInteger('form_id');
                $table->unsignedTinyInteger('permission_type'); // 1=view, 2=add/edit/update, 3=full
                $table->timestamps();

                $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
                $table->foreign('form_id')->references('id')->on('forms')->onDelete('cascade');
                $table->unique(['role_id', 'form_id']);
            });
        }

        // Extend roles table if needed
        if (Schema::hasTable('roles')) {
            Schema::table('roles', function (Blueprint $table) {
                if (!Schema::hasColumn('roles', 'code')) {
                    $table->string('code')->nullable()->after('name');
                }
                if (!Schema::hasColumn('roles', 'is_active')) {
                    $table->boolean('is_active')->default(true)->after('code');
                }
            });
        }

        // Extend users table with role_id and is_admin
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'role_id')) {
                    $table->unsignedBigInteger('role_id')->nullable()->after('id');
                    $table->foreign('role_id')->references('id')->on('roles')->onDelete('set null');
                }
                if (!Schema::hasColumn('users', 'is_admin')) {
                    $table->boolean('is_admin')->default(false)->after('remember_token');
                }
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('role_form_permissions')) {
            Schema::dropIfExists('role_form_permissions');
        }

        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (Schema::hasColumn('users', 'role_id')) {
                    $table->dropForeign(['role_id']);
                    $table->dropColumn('role_id');
                }
                if (Schema::hasColumn('users', 'is_admin')) {
                    $table->dropColumn('is_admin');
                }
            });
        }

        if (Schema::hasTable('roles')) {
            Schema::table('roles', function (Blueprint $table) {
                if (Schema::hasColumn('roles', 'code')) {
                    $table->dropColumn('code');
                }
                if (Schema::hasColumn('roles', 'is_active')) {
                    $table->dropColumn('is_active');
                }
            });
        }
    }
};


