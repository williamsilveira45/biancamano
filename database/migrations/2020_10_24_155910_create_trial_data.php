<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrialData extends Migration
{
    const TABLE_NAME = 'trial_data';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->id();
            $table->integer('codigo')->index();
            $table->string('cliente');
            $table->string('documento');
            $table->date('emissao_nota');
            $table->date('data_vencimento_original')->index();
            $table->date('data_vencimento')->index();
            $table->date('competencia')->index();
            $table->string('natureza_financeira');
            $table->string('operacao');
            $table->string('titulo_pago')->index();
            $table->decimal('valor_original', 12, 2);
            $table->decimal('multa_aplicada', 12, 2);
            $table->decimal('juros_aplicada', 12, 2);
            $table->decimal('desconto_aplicado', 12, 2);
            $table->decimal('valor_recebido', 12, 2);
            $table->date('data_entrada')->index();
            $table->string('emissora_titulo');
            $table->string('emissora_nota');
            $table->bigInteger('id_titulo')->index();
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
        Schema::dropIfExists(self::TABLE_NAME);
    }
}
