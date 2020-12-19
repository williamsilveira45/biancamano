<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnFileChecksumVencimentoSummaries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vencimento_summaries', function (Blueprint $table) {
            $table->string('file_checksum')->index('file_checksum_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vencimento_summaries', function (Blueprint $table) {
            $table->dropColumn('file_checksum');
            $table->dropIndex('file_checksum_index');
        });
    }
}
