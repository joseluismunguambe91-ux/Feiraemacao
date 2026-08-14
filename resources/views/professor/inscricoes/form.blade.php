@extends('layouts.professor')

@section('titulo', $inscricao->exists ? 'Editar inscrição' : 'Nova inscrição')

@section('conteudo')
<h1 class="h3 mb-1">{{ $inscricao->exists ? 'Editar a minha inscrição' : 'Inscrever-me na feira' }}</h1>
<p class="text-body-secondary mb-3">{{ $feira->tema }}</p>

<div class="aviso-destaque mb-4">
    <p class="titulo mb-1">Não te esqueças: um livro para a biblioteca</p>
    <p class="small mb-0">
        Depois de te inscreveres, entrega <strong>um livro na secretaria da escola</strong> — é a condição
        para a tua inscrição ser aprovada. Cada livro ajuda a encher a biblioteca da escola de mais histórias!
    </p>
</div>

<form method="POST"
      action="{{ $inscricao->exists ? route('professor.inscricoes.update', $inscricao) : route('professor.inscricoes.store') }}"
      enctype="multipart/form-data" style="max-width: 40rem;">
    @csrf
    @if ($inscricao->exists)
        @method('PUT')
    @endif

    @if (auth()->user()->hasRole('aluno'))
        <input type="hidden" name="tipo_participante" value="aluno">
        @php
            $classeAtual = auth()->user()->alunoLigado?->classe ?? auth()->user()->classe;
            $turmaAtual = auth()->user()->alunoLigado?->turma ?? auth()->user()->turma;
        @endphp
        <div class="alert alert-info small">
            Estás a inscrever-te como <strong>{{ auth()->user()->alunoLigado?->nome ?? auth()->user()->name }}</strong>
            @if ($classeAtual || $turmaAtual)
                ({{ $classeAtual ? $classeAtual.' classe, ' : '' }}turma {{ $turmaAtual }}).
            @else
                .
            @endif
        </div>
    @else
        <h2 class="h6 text-uppercase text-body-secondary mt-4">Em nome de quem é esta inscrição?</h2>
        <div class="mb-3">
            <label for="tipo_participante" class="form-label">Quem vai participar</label>
            <select id="tipo_participante" name="tipo_participante" class="form-select @error('tipo_participante') is-invalid @enderror" required>
                <option value="professor" {{ old('tipo_participante', $inscricao->tipo_participante) === 'professor' ? 'selected' : '' }}>Eu, o Professor</option>
                <option value="aluno" {{ old('tipo_participante', $inscricao->tipo_participante) === 'aluno' ? 'selected' : '' }}>Um ou mais alunos meus</option>
            </select>
            @error('tipo_participante')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label class="form-label">Escolhe o(s) aluno(s) <span class="text-body-secondary">(só se escolheste "Um ou mais alunos meus")</span></label>
            @if ($alunosDoProfessor->isEmpty())
                <p class="form-text text-warning mb-0">
                    Ainda não tens nenhum aluno registado — <a href="{{ route('professor.alunos.create') }}">regista um aluno</a> primeiro.
                </p>
            @else
                @php $alunosSelecionados = old('alunos', $inscricao->exists ? $inscricao->alunos->pluck('id')->all() : []); @endphp
                <div class="row g-1">
                    @foreach ($alunosDoProfessor as $aluno)
                        <div class="col-6 form-check">
                            <input type="checkbox" name="alunos[]" value="{{ $aluno->id }}" class="form-check-input" id="aluno_{{ $aluno->id }}"
                                   {{ in_array($aluno->id, $alunosSelecionados) ? 'checked' : '' }}>
                            <label class="form-check-label" for="aluno_{{ $aluno->id }}">{{ $aluno->nome }} ({{ $aluno->classe ? $aluno->classe.' ' : '' }}{{ $aluno->turma }})</label>
                        </div>
                    @endforeach
                </div>
            @endif
            @error('alunos')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label for="turma" class="form-label">A tua turma <span class="text-body-secondary">(só se escolheste "Eu, o Professor")</span></label>
            <input id="turma" name="turma" class="form-control @error('turma') is-invalid @enderror" value="{{ old('turma', $inscricao->turma) }}">
            @error('turma')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    @endif

    <h2 class="h6 text-uppercase text-body-secondary mt-4">Como te podemos contactar?</h2>
    <div class="mb-3">
        <label for="telefone" class="form-label">Número de telefone</label>
        <input id="telefone" name="telefone" class="form-control @error('telefone') is-invalid @enderror" value="{{ old('telefone', $inscricao->telefone) }}" required>
        @error('telefone')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $inscricao->email) }}" required>
        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <h2 class="h6 text-uppercase text-body-secondary mt-4">O que vais fazer na feira?</h2>
    <div class="mb-3">
        <label for="tipo_atividade" class="form-label">Escolhe uma opção</label>
        <select id="tipo_atividade" name="tipo_atividade" class="form-select @error('tipo_atividade') is-invalid @enderror" required>
            @foreach (['teatro', 'danca', 'musica', 'poesia', 'ciencias', 'artesanato', 'pintura', 'jogos', 'gastronomia', 'outro'] as $tipo)
                <option value="{{ $tipo }}" {{ old('tipo_atividade', $inscricao->tipo_atividade) === $tipo ? 'selected' : '' }}>
                    <x-tipo-atividade-label :tipo="$tipo" />
                </option>
            @endforeach
        </select>
        <div class="form-text">
            Escolhe <strong>Gastronomia</strong> se queres vender ou mostrar comida numa banca.
            Para as outras opções, vais fazer uma apresentação num horário marcado.
        </div>
        @error('tipo_atividade')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3">
        <label for="descricao" class="form-label">Conta-nos mais sobre isto</label>
        <textarea id="descricao" name="descricao" rows="3" class="form-control @error('descricao') is-invalid @enderror" placeholder="Ex.: Vamos apresentar uma dança tradicional com 5 colegas.">{{ old('descricao', $inscricao->descricao) }}</textarea>
        @error('descricao')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3">
        <label for="numero_participantes" class="form-label">Quantas pessoas participam (contigo incluído)?</label>
        <input type="number" min="1" id="numero_participantes" name="numero_participantes"
               class="form-control @error('numero_participantes') is-invalid @enderror"
               value="{{ old('numero_participantes', $inscricao->numero_participantes) }}" required>
        @error('numero_participantes')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div data-secao="gastronomia">
        <h2 class="h6 text-uppercase text-body-secondary mt-4">O teu prato</h2>
        <div class="row g-3 mb-3">
            <div class="col-8">
                <label for="produto_nome" class="form-label">Nome do prato</label>
                <input id="produto_nome" name="produto_nome" class="form-control @error('produto_nome') is-invalid @enderror" value="{{ old('produto_nome', $inscricao->produto_nome) }}" placeholder="Ex.: Bolo de coco">
                @error('produto_nome')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-4">
                <label for="produto_preco" class="form-label">Preço (MT)</label>
                <input type="number" min="0" step="0.01" id="produto_preco" name="produto_preco" class="form-control @error('produto_preco') is-invalid @enderror" value="{{ old('produto_preco', $inscricao->produto_preco) }}">
                @error('produto_preco')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="mb-3">
            <label for="produto_foto" class="form-label">Uma foto do prato <span class="text-body-secondary">(opcional, mas ajuda muito!)</span></label>
            <input type="file" id="produto_foto" name="produto_foto" class="form-control @error('produto_foto') is-invalid @enderror" accept="image/*">
            <div class="form-text">Assim que a tua inscrição for aprovada, esta foto aparece no site para toda a gente ver o que vais vender.</div>
            @error('produto_foto')<div class="invalid-feedback">{{ $message }}</div>@enderror
            @if ($inscricao->exists && $inscricao->produto_foto_path)
                <div class="mt-2"><a href="{{ \Illuminate\Support\Facades\Storage::url($inscricao->produto_foto_path) }}" target="_blank" class="small">Ver a foto que já enviaste</a></div>
            @endif
        </div>
    </div>

    <div data-secao="atividade">
        <h2 class="h6 text-uppercase text-body-secondary mt-4">Precisas de alguma coisa especial?</h2>
        <p class="form-text mt-0">Marca só o que realmente precisas — a Comissão Organizadora prepara isso para ti.</p>
        <div class="row g-2 mb-3">
            @foreach (['necessita_palco' => 'Palco', 'necessita_eletricidade' => 'Eletricidade (tomada)', 'necessita_projetor' => 'Projetor', 'necessita_som' => 'Aparelhagem de som'] as $campo => $rotulo)
                <div class="col-6 form-check">
                    <input type="checkbox" name="{{ $campo }}" value="1" class="form-check-input" id="{{ $campo }}"
                           {{ old($campo, $inscricao->$campo ?? false) ? 'checked' : '' }}>
                    <label class="form-check-label" for="{{ $campo }}">{{ $rotulo }}</label>
                </div>
            @endforeach
        </div>
        <div class="row g-3 mb-3">
            <div class="col-6">
                <label for="numero_mesas" class="form-label">Quantas mesas?</label>
                <input type="number" min="0" id="numero_mesas" name="numero_mesas" class="form-control @error('numero_mesas') is-invalid @enderror" value="{{ old('numero_mesas', $inscricao->numero_mesas ?? 0) }}">
                @error('numero_mesas')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-6">
                <label for="numero_cadeiras" class="form-label">Quantas cadeiras?</label>
                <input type="number" min="0" id="numero_cadeiras" name="numero_cadeiras" class="form-control @error('numero_cadeiras') is-invalid @enderror" value="{{ old('numero_cadeiras', $inscricao->numero_cadeiras ?? 0) }}">
                @error('numero_cadeiras')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <h2 class="h6 text-uppercase text-body-secondary mt-4">A que horas preferes apresentar?</h2>
        <p class="form-text mt-0">É só um pedido — a Comissão Organizadora confirma a hora e o local certos depois de aprovar.</p>
        <div class="row g-3 mb-3">
            <div class="col-6">
                <label for="horario_pretendido" class="form-label">Hora que preferes</label>
                <input type="time" id="horario_pretendido" name="horario_pretendido" class="form-control @error('horario_pretendido') is-invalid @enderror" value="{{ old('horario_pretendido', $inscricao->horario_pretendido ? substr($inscricao->horario_pretendido, 0, 5) : '') }}">
                @error('horario_pretendido')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-6">
                <label for="duracao_minutos" class="form-label">Quanto tempo dura? (minutos)</label>
                <input type="number" min="1" id="duracao_minutos" name="duracao_minutos" class="form-control @error('duracao_minutos') is-invalid @enderror" value="{{ old('duracao_minutos', $inscricao->duracao_minutos) }}">
                @error('duracao_minutos')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>

    <h2 class="h6 text-uppercase text-body-secondary mt-4">Mais alguma coisa?</h2>
    <div class="mb-3">
        <label for="observacoes" class="form-label">Queres dizer-nos mais alguma coisa? <span class="text-body-secondary">(opcional)</span></label>
        <textarea id="observacoes" name="observacoes" rows="2" class="form-control @error('observacoes') is-invalid @enderror">{{ old('observacoes', $inscricao->observacoes) }}</textarea>
        @error('observacoes')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-4">
        <label for="fotos" class="form-label">Queres juntar fotos? <span class="text-body-secondary">(opcional)</span></label>
        <input type="file" id="fotos" name="fotos[]" class="form-control @error('fotos.*') is-invalid @enderror" accept="image/*" multiple>
        @error('fotos.*')<div class="invalid-feedback">{{ $message }}</div>@enderror
        @if ($inscricao->exists && $inscricao->fotos->isNotEmpty())
            <div class="d-flex gap-2 flex-wrap mt-2">
                @foreach ($inscricao->fotos as $foto)
                    <a href="{{ \Illuminate\Support\Facades\Storage::url($foto->path) }}" target="_blank" class="small">Foto #{{ $loop->iteration }}</a>
                @endforeach
            </div>
        @endif
    </div>

    <button type="submit" class="btn btn-primary">{{ $inscricao->exists ? 'Guardar alterações' : 'Enviar a minha inscrição' }}</button>
    <a href="{{ route('professor.inscricoes.index') }}" class="btn btn-outline-secondary">Cancelar</a>
</form>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var selectTipo = document.getElementById('tipo_atividade');
        var secaoGastronomia = document.querySelector('[data-secao="gastronomia"]');
        var secaoAtividade = document.querySelector('[data-secao="atividade"]');

        function atualizarSeccoesVisiveis() {
            var ehGastronomia = selectTipo.value === 'gastronomia';
            secaoGastronomia.classList.toggle('d-none', ! ehGastronomia);
            secaoAtividade.classList.toggle('d-none', ehGastronomia);
        }

        selectTipo.addEventListener('change', atualizarSeccoesVisiveis);
        atualizarSeccoesVisiveis();
    });
</script>
@endpush
