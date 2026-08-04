<?php

namespace App\Models;

use App\Models\Concerns\PertenceAFeira;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Atividade extends Model
{
    use HasFactory, PertenceAFeira, SoftDeletes;

    protected $fillable = [
        'feira_id', 'inscricao_id', 'tipo', 'titulo', 'descricao',
        'responsavel_id', 'participantes_previstos', 'foto_path', 'estado',
    ];

    public function inscricao(): BelongsTo
    {
        return $this->belongsTo(Inscricao::class);
    }

    public function responsavel(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsavel_id');
    }

    public function itensProgramacao(): HasMany
    {
        return $this->hasMany(ProgramacaoItem::class);
    }
}
