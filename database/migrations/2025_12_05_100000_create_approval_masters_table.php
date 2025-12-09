<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('approval_masters', function (Blueprint $table) {
            $table->id();
            $table->string('form_name')->unique()->comment('Name of the form (e.g., customer_orders, production_orders)');
            $table->string('display_name')->comment('Human-readable name (e.g., Customer Orders, Production Orders)');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approval_masters');
    }
};

