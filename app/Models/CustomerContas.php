<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerContas extends Model
{
    protected $table = 'customer_contas';

    protected $fillable = [
        'customer_id',
        'conta_id',
        'nome_csv',
        'nome_sha1',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function planoConta()
    {
        return $this->belongsTo(PlanoConta::class, 'conta_id', 'id');
    }
}
