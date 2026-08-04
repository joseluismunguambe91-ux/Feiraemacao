<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InscricaoFoto extends Model
{
    protected $fillable = ['inscricao_id', 'path'];

    public function inscricao(): BelongsTo
    {
        return $this->belongsTo(Inscricao::class);
    }
}
