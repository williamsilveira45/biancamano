<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVencimentoSummariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vencimento_summaries', function (Blueprint $table) {
            $table->id();
            $table->date('emissao_nota');
            $table->date('data_vencimento_original')->index();
            $table->date('competencia');
            $table->string('natureza_financeira');
            $table->string('operacao');
            $table->decimal('valor', 12, 2);
            $table->date('data_entrada');
            $table->string('emissora_titulo');
            $table->string('titulo_pago');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vencimento_summaries');
    }
}
