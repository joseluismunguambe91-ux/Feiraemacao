<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Visita extends Model
{
    use HasFactory;

    protected $fillable = ['feira_id', 'sessao_id', 'data'];

    protected $casts = [
        'data' => 'date',
    ];

    public function feira(): BelongsTo
    {
        return $this->belongsTo(Feira::class);
    }
}
