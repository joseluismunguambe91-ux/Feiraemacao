<?php

namespace App\Models;

use App\Models\Concerns\PertenceAFeira;
use Illuminate\Database\Eloquent\Model;

class MensagemContacto extends Model
{
    use PertenceAFeira;

    protected $table = 'mensagens_contacto';

    protected $fillable = ['feira_id', 'nome', 'email', 'assunto', 'mensagem', 'lida'];

    protected $casts = [
        'lida' => 'boolean',
    ];
}
