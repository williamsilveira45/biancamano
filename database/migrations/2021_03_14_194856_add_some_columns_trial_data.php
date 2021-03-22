<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSomeColumnsTrialData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trial_data', function (Blueprint $table) {
            $table->date('formatted_emissao_nota')->nullable();
            $table->date('formatted_data_vencimento_original')->nullable();
            $table->date('formatted_competencia')->nullable();
            $table->date('formatted_data_entrada')->nullable();
            $table->index([
                'formatted_emissao_nota',
                'formatted_data_vencimento_original',
                'formatted_competencia',
                'formatted_data_entrada',
                'file_checksum',
                'customer_id',

            ], 'index_composto');
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
            $table->dropColumn('formatted_emissao_nota');
            $table->dropColumn('formatted_data_vencimento_original');
            $table->dropColumn('formatted_competencia');
            $table->dropColumn('formatted_data_entrada');
            $table->dropIndex('index_composto');
        });
    }
}
