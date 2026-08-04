<?php

namespace App\Models;

use App\Models\Concerns\PertenceAFeira;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class GastronomiaItem extends Model
{
    use HasFactory, PertenceAFeira, SoftDeletes;

    protected $table = 'gastronomia_itens';

    protected $fillable = [
        'feira_id', 'inscricao_id', 'nome', 'categoria', 'descricao', 'preco',
        'foto_path', 'ingredientes', 'disponivel', 'quantidade_disponivel',
    ];

    protected $casts = [
        'preco' => 'decimal:2',
        'disponivel' => 'boolean',
    ];

    public function inscricao(): BelongsTo
    {
        return $this->belongsTo(Inscricao::class);
    }
}
