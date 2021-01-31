<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsContaIdAndContaSha1AtSumarizadaVencimento extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vencimento_summaries', function (Blueprint $table) {
            $table->unsignedBigInteger('conta_id')->index('conta_idIndexS');
            $table->string('conta_sha1', 40)->index('conta_sha1IndexS')->nullable('');
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
            $table->dropColumn('conta_id');
            $table->dropColumn('conta_sha1');
            $table->dropIndex('conta_idIndexS');
            $table->dropIndex('conta_sha1IndexS');
        });
    }
}
