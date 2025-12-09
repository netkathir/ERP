<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('purchase_indents', function (Blueprint $table) {
            $table->id();
            $table->string('indent_no')->unique();
            $table->date('indent_date');
            $table->string('upload_path')->nullable();
            $table->string('upload_original_name')->nullable();
            $table->string('status')->default('Pending'); // Pending, Submitted, Approved, Rejected
            $table->text('remarks')->nullable();

            $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('set null');
            $table->unsignedBigInteger('created_by_id')->nullable();
            $table->unsignedBigInteger('updated_by_id')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_indents');
    }
};


