<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PlanoConta
 *
 * @property int $id
 * @property string $nome_conta
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @mixin \Eloquent
 */
class PlanoConta extends Model
{
    protected $fillable = [
      'nome_conta',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function customerContas()
    {
        return $this->hasMany(CustomerContas::class, 'conta_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function vencimentoSummaries()
    {
        return $this->hasMany(VencimentoSummary::class, 'conta_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function trialData()
    {
        return $this->hasMany(TrialData::class, 'conta_id', 'id');
    }
}

