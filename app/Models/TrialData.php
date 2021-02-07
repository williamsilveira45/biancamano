<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\TrialData
 *
 * @property int $id
 * @property int $codigo
 * @property string $cliente
 * @property string $documento
 * @property string $emissao_nota
 * @property string $data_vencimento_original
 * @property string $data_vencimento
 * @property string $competencia
 * @property string $natureza_financeira
 * @property string $operacao
 * @property string $titulo_pago
 * @property string $valor_original
 * @property string $multa_aplicada
 * @property string $juros_aplicada
 * @property string $desconto_aplicado
 * @property string $valor_recebido
 * @property string $data_entrada
 * @property string $emissora_titulo
 * @property string $emissora_nota
 * @property int $id_titulo
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|TrialData newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TrialData newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TrialData query()
 * @method static \Illuminate\Database\Eloquent\Builder|TrialData whereCliente($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TrialData whereCodigo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TrialData whereCompetencia($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TrialData whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TrialData whereDataEntrada($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TrialData whereDataVencimento($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TrialData whereDataVencimentoOriginal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TrialData whereDescontoAplicado($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TrialData whereDocumento($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TrialData whereEmissaoNota($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TrialData whereEmissoraNota($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TrialData whereEmissoraTitulo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TrialData whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TrialData whereIdTitulo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TrialData whereJurosAplicada($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TrialData whereMultaAplicada($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TrialData whereNaturezaFinanceira($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TrialData whereOperacao($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TrialData whereTituloPago($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TrialData whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TrialData whereValorOriginal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TrialData whereValorRecebido($value)
 * @mixin \Eloquent
 */
class TrialData extends Model
{
    protected $table = 'trial_data';

    protected $fillable = [
        'codigo',
        'cliente',
        'documento',
        'emissao_nota',
        'data_vencimento_original',
        'data_vencimento',
        'competencia',
        'natureza_financeira',
        'operacao',
        'titulo_pago',
        'valor_original',
        'multa_aplicada',
        'juros_aplicada',
        'desconto_aplicado',
        'valor_recebido',
        'data_entrada',
        'emissora_titulo',
        'emissora_nota',
        'id_titulo',
        'file_checksum',
        'counter_id'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function file()
    {
        return $this->belongsTo(File::class, 'file_checksum', 'sha1_checksum');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function trialData()
    {
        return $this->belongsTo(TrialData::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function summariesVencimento()
    {
        return $this->belongsTo(VencimentoSummary::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function contaCustomer()
    {
        return $this->belongsTo(PlanoConta::class, 'conta_id', 'id');
    }
}
