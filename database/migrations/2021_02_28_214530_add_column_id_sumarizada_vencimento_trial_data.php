<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnIdSumarizadaVencimentoTrialData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trial_data', function (Blueprint $table) {
            $table->unsignedBigInteger('id_summary_vencimento')
                ->nullable()
                ->index('id_summary_vencimento');
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
            $table->dropColumn('id_summary_vencimento');
            $table->dropIndex('id_summary_vencimento');
        });
    }
}
