<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    const UPDATED_AT = null;

    protected $fillable = [
        'user_id', 'feira_id', 'acao', 'entidade_tipo', 'entidade_id',
        'dados_antigos', 'dados_novos', 'ip_address', 'user_agent',
    ];

    protected $casts = [
        'dados_antigos' => 'array',
        'dados_novos' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function feira(): BelongsTo
    {
        return $this->belongsTo(Feira::class);
    }
}
