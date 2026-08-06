<?php

namespace App\Models;

use App\Models\Concerns\Auditavel;
use App\Models\Concerns\PertenceAFeira;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inscricao extends Model
{
    use Auditavel, HasFactory, PertenceAFeira, SoftDeletes;

    protected $table = 'inscricoes';

    protected $fillable = [
        'feira_id', 'professor_id', 'tipo_participante', 'turma', 'classe', 'telefone', 'email',
        'tipo_atividade', 'descricao', 'produto_nome', 'produto_preco', 'produto_foto_path', 'numero_participantes',
        'necessita_palco', 'necessita_eletricidade', 'necessita_projetor', 'necessita_som',
        'numero_mesas', 'numero_cadeiras', 'horario_pretendido', 'duracao_minutos',
        'observacoes', 'estado', 'comentario_avaliacao', 'avaliado_por', 'avaliado_em',
    ];

    protected $casts = [
        'necessita_palco' => 'boolean',
        'necessita_eletricidade' => 'boolean',
        'necessita_projetor' => 'boolean',
        'necessita_som' => 'boolean',
        'produto_preco' => 'decimal:2',
        'avaliado_em' => 'datetime',
    ];

    public function professor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'professor_id');
    }

    public function avaliadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'avaliado_por');
    }

    public function fotos(): HasMany
    {
        return $this->hasMany(InscricaoFoto::class);
    }

    public function alunos(): BelongsToMany
    {
        return $this->belongsToMany(Aluno::class, 'inscricao_aluno');
    }

    public function atividade(): HasOne
    {
        return $this->hasOne(Atividade::class);
    }

    public function expositor(): HasOne
    {
        return $this->hasOne(Expositor::class);
    }
}
