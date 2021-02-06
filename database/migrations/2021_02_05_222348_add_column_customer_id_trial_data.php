<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnCustomerIdTrialData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trial_data', function (Blueprint $table) {
            $table->unsignedBigInteger('customer_id')->index('conta_customer_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trial_data', function (Blueprint $table) {
            $table->dropColumn('customer_id');
            $table->dropIndex('conta_customer_id');
        });
    }
}
