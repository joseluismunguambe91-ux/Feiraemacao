<?php

namespace App\Http\Requests\Painel;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GaleriaItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tipo = $this->input('tipo');
        $aCriar = $this->route('item') === null;

        return [
            'tipo' => ['required', Rule::in(['foto', 'video'])],
            'categoria' => ['nullable', 'string', 'max:80'],
            'titulo' => ['nullable', 'string', 'max:150'],
            'foto' => [$tipo === 'foto' && $aCriar ? 'required' : 'nullable', 'image', 'max:4096'],
            'url_video' => [$tipo === 'video' ? 'required' : 'nullable', 'url', 'max:255'],
            'ordem' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
