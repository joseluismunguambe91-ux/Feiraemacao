<?php

namespace App\Models;

use App\Models\Concerns\PertenceAFeira;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgramacaoItem extends Model
{
    use HasFactory, PertenceAFeira;

    protected $table = 'programacao_itens';

    protected $fillable = ['feira_id', 'atividade_id', 'data', 'hora_inicio', 'hora_fim', 'local', 'palco'];

    protected $casts = [
        'data' => 'date',
    ];

    public function atividade(): BelongsTo
    {
        return $this->belongsTo(Atividade::class);
    }
}
