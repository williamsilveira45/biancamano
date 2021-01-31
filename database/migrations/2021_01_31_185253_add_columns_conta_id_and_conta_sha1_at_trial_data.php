<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsContaIdAndContaSha1AtTrialData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trial_data', function (Blueprint $table) {
            $table->unsignedBigInteger('conta_id')->index('conta_idIndex');
            $table->string('conta_sha1', 40)->index('conta_sha1Index');
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
            $table->dropColumn('conta_id');
            $table->dropColumn('conta_sha1');
            $table->dropIndex('conta_idIndex');
            $table->dropIndex('conta_sha1Index');
        });
    }
}
