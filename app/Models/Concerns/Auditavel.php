<?php

namespace App\Models\Concerns;

use App\Models\AuditLog;

/**
 * Aplicado só aos Models sensíveis definidos na Etapa 7 (Feira, Inscricao,
 * User, Stand) — grava em audit_logs sem repetir a mesma lógica de log em
 * cada Controller/Service que os altera.
 */
trait Auditavel
{
    public static function bootAuditavel(): void
    {
        static::created(fn ($model) => $model->registarAuditoria('criado', null, $model->getAttributes()));
        static::updated(fn ($model) => $model->registarAuditoria('atualizado', $model->getOriginal(), $model->getChanges()));
        static::deleted(fn ($model) => $model->registarAuditoria('eliminado', $model->getOriginal(), null));
    }

    protected function registarAuditoria(string $acao, ?array $antigos, ?array $novos): void
    {
        AuditLog::create([
            'user_id' => auth()->id(),
            'feira_id' => $this->attributes['feira_id'] ?? null,
            'acao' => class_basename(static::class).'.'.$acao,
            'entidade_tipo' => class_basename(static::class),
            'entidade_id' => $this->getKey(),
            'dados_antigos' => $antigos,
            'dados_novos' => $novos,
            'ip_address' => request()->ip(),
            'user_agent' => substr((string) request()->userAgent(), 0, 255),
        ]);
    }
}
