<?php

namespace App\Http\Requests\Painel;

use Illuminate\Foundation\Http\FormRequest;

class PatrocinadorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $aCriar = $this->route('patrocinador') === null;

        return [
            'nome' => ['required', 'string', 'max:120'],
            'logotipo' => [$aCriar ? 'required' : 'nullable', 'image', 'max:2048'],
            'url_site' => ['nullable', 'url', 'max:255'],
            'nivel' => ['nullable', 'string', 'max:40'],
            'ordem' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
