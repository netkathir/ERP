<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        if (!Schema::hasTable('menus')) {
            Schema::create('menus', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('code')->unique(); // e.g. masters, purchase
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('submenus')) {
            Schema::create('submenus', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('menu_id');
                $table->string('name');
                $table->string('code'); // unique within a menu
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->foreign('menu_id')->references('id')->on('menus')->onDelete('cascade');
                $table->unique(['menu_id', 'code']);
            });
        }

        if (!Schema::hasTable('forms')) {
            Schema::create('forms', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('menu_id');
                $table->unsignedBigInteger('submenu_id')->nullable();
                $table->string('name');
                $table->string('code')->unique();        // e.g. purchase_orders_form
                $table->string('route_name')->nullable(); // e.g. purchase-orders.index
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->foreign('menu_id')->references('id')->on('menus')->onDelete('cascade');
                $table->foreign('submenu_id')->references('id')->on('submenus')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('forms');
        Schema::dropIfExists('submenus');
        Schema::dropIfExists('menus');
    }
};


