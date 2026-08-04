<?php

namespace App\Models;

use App\Models\Concerns\Auditavel;
use App\Models\Concerns\PertenceAFeira;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Stand extends Model
{
    use Auditavel, HasFactory, PertenceAFeira, SoftDeletes;

    protected $fillable = [
        'feira_id', 'numero', 'localizacao', 'capacidade',
        'responsavel_id', 'categoria', 'estado', 'qr_token',
    ];

    public function responsavel(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsavel_id');
    }

    public function expositor(): HasOne
    {
        return $this->hasOne(Expositor::class);
    }
}
