<?php

namespace App\Models;

use App\Models\Concerns\PertenceAFeira;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expositor extends Model
{
    use HasFactory, PertenceAFeira, SoftDeletes;

    protected $table = 'expositores';

    protected $fillable = [
        'feira_id', 'professor_id', 'inscricao_id', 'turma', 'categoria',
        'descricao', 'stand_id', 'estado',
    ];

    public function professor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'professor_id');
    }

    public function inscricao(): BelongsTo
    {
        return $this->belongsTo(Inscricao::class);
    }

    public function stand(): BelongsTo
    {
        return $this->belongsTo(Stand::class);
    }

    public function fotos(): HasMany
    {
        return $this->hasMany(ExpositorFoto::class);
    }
}
