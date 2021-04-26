<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class VencimentoSummary
 * @package App\Models
 * @method checksum(string $checksum)
 * @method ano(int $ano)
 */
class VencimentoSummary extends Model
{
    protected $casts = [
        'emissao_nota' => 'date',
        'data_vencimento_original' => 'date',
        'competencia' => 'date',
    ];

    /**
     * @param $query
     * @param string $checksum
     * @return mixed
     */
    public function scopeChecksum($query, $checksum)
    {
        return $query->where('file_checksum', $checksum);
    }

    /**
     * @param $query
     * @param $ano
     * @return mixed
     */
    public function scopeAno($query, $ano)
    {
        return $query->whereRaw('YEAR(data_vencimento_original) = ' . $ano);
    }

    /**
     * @param $query
     * @param $mes
     * @return mixed
     */
    public function scopeMonth($query, $mes)
    {
        return $query->whereRaw('MONTH(data_vencimento_original) = ' . $mes);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function file()
    {
        return $this->belongsTo(File::class, 'sha1_checksum', 'file_checksum');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function contaCustomer()
    {
        return $this->belongsTo(PlanoConta::class, 'conta_id', 'id');
    }
}
