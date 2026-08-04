<?php

namespace App\Models;

use App\Models\Concerns\PertenceAFeira;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GaleriaItem extends Model
{
    use HasFactory, PertenceAFeira, SoftDeletes;

    protected $table = 'galeria_itens';

    protected $fillable = ['feira_id', 'tipo', 'categoria', 'titulo', 'path_ou_url', 'ordem'];
}
