<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->string('transaction_type'); // e.g., 'sale', 'purchase', 'transfer', etc.
            $table->string('reference_number')->unique();
            $table->text('description')->nullable();
            $table->decimal('amount', 15, 2);
            $table->string('status')->default('pending'); // pending, completed, cancelled
            $table->json('metadata')->nullable(); // Additional transaction data
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
        Schema::dropIfExists('transactions');
    }
}
