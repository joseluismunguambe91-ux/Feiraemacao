@extends('layouts.painel')

@section('titulo', 'Rever inscrição')

@section('conteudo')
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h1 class="h3 mb-1"><x-tipo-atividade-label :tipo="$inscricao->tipo_atividade" /></h1>
        <x-estado-inscricao-badge :estado="$inscricao->estado" />
    </div>
    <a href="{{ route('painel.inscricoes.index') }}" class="btn btn-outline-secondary btn-sm">← Voltar à lista</a>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <h2 class="h6 text-uppercase text-body-secondary">Dados do responsável</h2>
        <dl class="row small">
            <dt class="col-sm-4">Professor</dt><dd class="col-sm-8">{{ $inscricao->professor->name }}</dd>
            <dt class="col-sm-4">Em nome de</dt><dd class="col-sm-8">{{ $inscricao->tipo_participante === 'aluno' ? 'Aluno(s)' : 'Professor' }}</dd>
            <dt class="col-sm-4">Turma</dt><dd class="col-sm-8">{{ $inscricao->turma ?? '—' }}</dd>
            <dt class="col-sm-4">Telefone</dt><dd class="col-sm-8">{{ $inscricao->telefone }}</dd>
            <dt class="col-sm-4">Email</dt><dd class="col-sm-8">{{ $inscricao->email }}</dd>
        </dl>

        <h2 class="h6 text-uppercase text-body-secondary mt-4">Atividade</h2>
        <p class="small">{{ $inscricao->descricao ?? 'Sem descrição.' }}</p>
        <dl class="row small">
            @if ($inscricao->tipo_atividade === 'gastronomia')
                <dt class="col-sm-4">Prato proposto</dt>
                <dd class="col-sm-8">
                    {{ $inscricao->produto_nome }} — {{ number_format($inscricao->produto_preco, 2, ',', '.') }} MT
                    @if ($inscricao->produto_foto_path)
                        <br>
                        <a href="{{ \Illuminate\Support\Facades\Storage::url($inscricao->produto_foto_path) }}" target="_blank">
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($inscricao->produto_foto_path) }}" alt="{{ $inscricao->produto_nome }}" class="rounded mt-1" style="max-width: 120px; max-height: 90px; object-fit: cover;">
                        </a>
                    @endif
                </dd>
            @endif
            <dt class="col-sm-4">Participantes</dt><dd class="col-sm-8">{{ $inscricao->numero_participantes }}</dd>
            <dt class="col-sm-4">Necessidades</dt>
            <dd class="col-sm-8">
                @php
                    $necessidades = array_filter([
                        'Palco' => $inscricao->necessita_palco,
                        'Eletricidade' => $inscricao->necessita_eletricidade,
                        'Projetor' => $inscricao->necessita_projetor,
                        'Som' => $inscricao->necessita_som,
                    ]);
                @endphp
                {{ $necessidades ? implode(', ', array_keys($necessidades)) : 'Nenhuma' }}
                — {{ $inscricao->numero_mesas }} mesa(s), {{ $inscricao->numero_cadeiras }} cadeira(s)
            </dd>
            <dt class="col-sm-4">Horário pretendido</dt>
            <dd class="col-sm-8">
                {{ $inscricao->horario_pretendido ? substr($inscricao->horario_pretendido, 0, 5) : '—' }}
                @if ($inscricao->duracao_minutos) ({{ $inscricao->duracao_minutos }} min) @endif
            </dd>
            <dt class="col-sm-4">Observações</dt><dd class="col-sm-8">{{ $inscricao->observacoes ?? '—' }}</dd>
        </dl>

        @if ($inscricao->fotos->isNotEmpty())
            <h2 class="h6 text-uppercase text-body-secondary mt-4">Fotografias</h2>
            <div class="d-flex gap-2 flex-wrap">
                @foreach ($inscricao->fotos as $foto)
                    <a href="{{ \Illuminate\Support\Facades\Storage::url($foto->path) }}" target="_blank" class="small">Foto #{{ $loop->iteration }}</a>
                @endforeach
            </div>
        @endif
    </div>

    <div class="col-lg-5">
        @if ($inscricao->estado === 'pendente')
            <div class="border rounded p-3 mb-3">
                @if ($inscricao->tipo_atividade === 'gastronomia')
                    <h2 class="h6">Aprovar e atribuir banca</h2>
                    <form method="POST" action="{{ route('painel.inscricoes.aprovar', $inscricao) }}">
                        @csrf
                        <div class="mb-2">
                            <label for="produto_nome" class="form-label">Nome do prato</label>
                            <input id="produto_nome" name="produto_nome" class="form-control form-control-sm @error('produto_nome') is-invalid @enderror" value="{{ old('produto_nome', $inscricao->produto_nome) }}" required>
                            @error('produto_nome')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="produto_preco" class="form-label">Preço (MT)</label>
                            <input type="number" min="0" step="0.01" id="produto_preco" name="produto_preco" class="form-control form-control-sm @error('produto_preco') is-invalid @enderror" value="{{ old('produto_preco', $inscricao->produto_preco) }}" required>
                            @error('produto_preco')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="stand_id" class="form-label">Stand</label>
                            <select id="stand_id" name="stand_id" class="form-select form-select-sm @error('stand_id') is-invalid @enderror" required>
                                <option value="">— Selecionar —</option>
                                @foreach ($standsLivres as $stand)
                                    <option value="{{ $stand->id }}" {{ (int) old('stand_id') === $stand->id ? 'selected' : '' }}>
                                        Stand {{ $stand->numero }}{{ $stand->localizacao ? ' · '.$stand->localizacao : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('stand_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            @if ($standsLivres->isEmpty())
                                <div class="form-text text-warning">Não há nenhum stand livre nesta edição — cria um em "Stands" antes de aprovar.</div>
                            @endif
                        </div>
                        <button type="submit" class="btn btn-success btn-sm w-100" {{ $standsLivres->isEmpty() ? 'disabled' : '' }}>Aprovar e atribuir banca</button>
                    </form>
                @else
                    <h2 class="h6">Aprovar e agendar</h2>
                    <form method="POST" action="{{ route('painel.inscricoes.aprovar', $inscricao) }}">
                        @csrf
                        <div class="mb-2">
                            <label for="titulo" class="form-label">Título para a programação</label>
                            <input id="titulo" name="titulo" class="form-control form-control-sm @error('titulo') is-invalid @enderror" value="{{ old('titulo') }}" required>
                            @error('titulo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-2">
                            <label for="data" class="form-label">Data</label>
                            <input type="date" id="data" name="data" class="form-control form-control-sm @error('data') is-invalid @enderror"
                                   value="{{ old('data', $inscricao->feira->data_inicio->format('Y-m-d')) }}"
                                   min="{{ $inscricao->feira->data_inicio->format('Y-m-d') }}"
                                   max="{{ $inscricao->feira->data_fim->format('Y-m-d') }}" required>
                            @error('data')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <label for="hora_inicio" class="form-label">Hora início</label>
                                <input type="time" id="hora_inicio" name="hora_inicio" class="form-control form-control-sm @error('hora_inicio') is-invalid @enderror"
                                       value="{{ old('hora_inicio', $inscricao->horario_pretendido ? substr($inscricao->horario_pretendido, 0, 5) : '') }}" required>
                                @error('hora_inicio')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-6">
                                <label for="hora_fim" class="form-label">Hora fim</label>
                                <input type="time" id="hora_fim" name="hora_fim" class="form-control form-control-sm @error('hora_fim') is-invalid @enderror"
                                       value="{{ old('hora_fim', $horaFimSugerida) }}" required>
                                @error('hora_fim')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="mb-2">
                            <label for="local" class="form-label">Local</label>
                            <input id="local" name="local" class="form-control form-control-sm @error('local') is-invalid @enderror" value="{{ old('local') }}" required>
                            @error('local')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="palco" class="form-label">Palco {{ $inscricao->necessita_palco ? '' : '(opcional)' }}</label>
                            <input id="palco" name="palco" class="form-control form-control-sm @error('palco') is-invalid @enderror" value="{{ old('palco') }}" {{ $inscricao->necessita_palco ? 'required' : '' }}>
                            @error('palco')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <button type="submit" class="btn btn-success btn-sm w-100">Aprovar e agendar</button>
                    </form>
                @endif
            </div>

            <div class="border rounded p-3">
                <h2 class="h6">Rejeitar</h2>
                <form method="POST" action="{{ route('painel.inscricoes.rejeitar', $inscricao) }}">
                    @csrf
                    <div class="mb-2">
                        <label for="comentario_avaliacao" class="form-label">Comentário (obrigatório)</label>
                        <textarea id="comentario_avaliacao" name="comentario_avaliacao" rows="3" class="form-control form-control-sm @error('comentario_avaliacao') is-invalid @enderror" required>{{ old('comentario_avaliacao') }}</textarea>
                        @error('comentario_avaliacao')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <button type="submit" class="btn btn-outline-danger btn-sm w-100">Rejeitar inscrição</button>
                </form>
            </div>
        @elseif ($inscricao->estado === 'aprovada' && $inscricao->atividade)
            <div class="border rounded p-3">
                <h2 class="h6">Agendamento confirmado</h2>
                <p class="small mb-1"><strong>{{ $inscricao->atividade->titulo }}</strong></p>
                @foreach ($inscricao->atividade->itensProgramacao as $item)
                    <p class="small text-body-secondary mb-0">
                        {{ $item->data->format('d/m/Y') }} · {{ substr($item->hora_inicio, 0, 5) }}–{{ substr($item->hora_fim, 0, 5) }}
                        · {{ $item->local }}{{ $item->palco ? ' · '.$item->palco : '' }}
                    </p>
                @endforeach
            </div>
        @elseif ($inscricao->estado === 'aprovada' && $inscricao->expositor)
            <div class="border rounded p-3">
                <h2 class="h6">Banca atribuída</h2>
                <p class="small mb-0">
                    Stand {{ $inscricao->expositor->stand->numero }}
                    {{ $inscricao->expositor->stand->localizacao ? ' · '.$inscricao->expositor->stand->localizacao : '' }}
                </p>
            </div>
        @elseif ($inscricao->estado === 'rejeitada')
            <div class="border rounded p-3">
                <h2 class="h6">Motivo da rejeição</h2>
                <p class="small mb-0">{{ $inscricao->comentario_avaliacao }}</p>
            </div>
        @endif
    </div>
</div>
@endsection
