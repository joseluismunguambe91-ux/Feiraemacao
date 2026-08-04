<?php

namespace App\Models\Concerns;

use App\Models\Feira;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Toda entidade operacional pertence a exatamente uma edição da feira (RN01,
 * docs/03-modelagem-base-dados.md). Este trait evita repetir a mesma relação
 * e o mesmo scope em cada um dos Models que referenciam feira_id.
 */
trait PertenceAFeira
{
    public function feira(): BelongsTo
    {
        return $this->belongsTo(Feira::class);
    }

    public function scopeDaFeira(Builder $query, int $feiraId): Builder
    {
        return $query->where('feira_id', $feiraId);
    }
}
