<?php

namespace App\Models;

use App\Models\Concerns\Auditavel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Feira extends Model
{
    use Auditavel, HasFactory, SoftDeletes;

    protected $fillable = [
        'tema', 'descricao', 'data_inicio', 'data_fim', 'hora_abertura',
        'hora_encerramento', 'local', 'banner_path', 'logotipo_path',
        'regulamento_path', 'estado',
    ];

    protected $casts = [
        'data_inicio' => 'date',
        'data_fim' => 'date',
    ];

    /** Edição publicada ou em curso (RN02 — garantido também ao nível da BD pela coluna estado_ativo). */
    public function scopeAtiva(Builder $query): Builder
    {
        return $query->whereNotNull('estado_ativo');
    }

    public function stands(): HasMany
    {
        return $this->hasMany(Stand::class);
    }

    public function expositores(): HasMany
    {
        return $this->hasMany(Expositor::class);
    }

    public function atividades(): HasMany
    {
        return $this->hasMany(Atividade::class);
    }

    public function gastronomiaItens(): HasMany
    {
        return $this->hasMany(GastronomiaItem::class);
    }

    public function inscricoes(): HasMany
    {
        return $this->hasMany(Inscricao::class);
    }

    public function programacaoItens(): HasMany
    {
        return $this->hasMany(ProgramacaoItem::class);
    }

    public function galeriaItens(): HasMany
    {
        return $this->hasMany(GaleriaItem::class);
    }

    public function patrocinadores(): HasMany
    {
        return $this->hasMany(Patrocinador::class);
    }

    public function relatoriosGerados(): HasMany
    {
        return $this->hasMany(RelatorioGerado::class);
    }

    public function mensagensContacto(): HasMany
    {
        return $this->hasMany(MensagemContacto::class);
    }
}
