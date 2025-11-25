<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuotationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->string('quotation_no')->unique();
            $table->date('date');
            $table->foreignId('customer_id')->constrained('customers');
            $table->text('billing_address')->nullable();
            $table->enum('gst_type', ['intra', 'inter']);
            $table->decimal('freight_charges', 10, 2)->default(0);
            $table->decimal('total_amount', 15, 2);
            $table->decimal('net_amount', 15, 2);
            $table->string('status')->default('draft');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quotations');
    }
}
