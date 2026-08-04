<?php

namespace App\Models;

use App\Models\Concerns\PertenceAFeira;
use Illuminate\Database\Eloquent\Model;

class Patrocinador extends Model
{
    use PertenceAFeira;

    protected $table = 'patrocinadores';

    protected $fillable = ['feira_id', 'nome', 'logotipo_path', 'url_site', 'nivel', 'ordem'];
}
