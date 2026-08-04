<?php

namespace App\Models;

use App\Models\Concerns\PertenceAFeira;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RelatorioGerado extends Model
{
    use PertenceAFeira;

    protected $table = 'relatorios_gerados';

    protected $fillable = ['feira_id', 'tipo', 'formato', 'filtros', 'path_ficheiro', 'gerado_por', 'estado'];

    protected $casts = [
        'filtros' => 'array',
    ];

    public function geradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'gerado_por');
    }
}
