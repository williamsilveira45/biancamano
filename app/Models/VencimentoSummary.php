<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VencimentoSummary extends Model
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function file()
    {
        return $this->belongsTo(File::class, 'sha1_checksum', 'file_checksum');
    }
}
