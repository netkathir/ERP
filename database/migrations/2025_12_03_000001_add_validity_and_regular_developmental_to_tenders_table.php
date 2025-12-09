<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddValidityAndRegularDevelopmentalToTendersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tenders', function (Blueprint $table) {
            // Store validity in days and regular/developmental flag
            $table->unsignedInteger('validity_of_offer_days')->nullable()->after('procure_from_approved_source');
            $table->string('regular_developmental', 50)->default('Regular')->after('validity_of_offer_days');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tenders', function (Blueprint $table) {
            $table->dropColumn(['validity_of_offer_days', 'regular_developmental']);
        });
    }
}


