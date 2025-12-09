<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('employee_code')->unique();
            $table->string('name');
            $table->foreignId('department_id')->constrained('departments')->onDelete('restrict');
            $table->foreignId('designation_id')->constrained('designations')->onDelete('restrict');
            $table->date('date_of_birth');
            $table->string('email')->unique();
            $table->string('mobile_no', 20);
            $table->enum('active', ['Yes', 'No'])->default('Yes');
            
            // Address fields
            $table->text('address_line_1')->nullable();
            $table->text('address_line_2')->nullable();
            $table->string('city');
            $table->string('state');
            $table->string('country')->default('India');
            $table->string('pincode', 10)->nullable();
            $table->string('emergency_contact_no', 20)->nullable();
            
            $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('set null');
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
        Schema::dropIfExists('employees');
    }
}
