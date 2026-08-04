<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Etapa 7, item 1: mensagens sempre genéricas (nunca revelar se é o email
 * ou a senha, nem se a conta está desativada) e rate limiting de 5
 * tentativas por minuto por email+IP.
 */
class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    public function autenticar(): void
    {
        $this->assegurarNaoBloqueado();

        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('lembrar'))) {
            RateLimiter::hit($this->chaveLimitador());

            throw ValidationException::withMessages([
                'email' => 'Credenciais inválidas.',
            ]);
        }

        if (! Auth::user()->ativo) {
            Auth::logout();

            throw ValidationException::withMessages([
                'email' => 'Credenciais inválidas.',
            ]);
        }

        RateLimiter::clear($this->chaveLimitador());
    }

    protected function assegurarNaoBloqueado(): void
    {
        if (! RateLimiter::tooManyAttempts($this->chaveLimitador(), 5)) {
            return;
        }

        event(new Lockout($this));

        $segundos = RateLimiter::availableIn($this->chaveLimitador());

        throw ValidationException::withMessages([
            'email' => "Demasiadas tentativas. Tenta novamente em {$segundos} segundos.",
        ]);
    }

    protected function chaveLimitador(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}
