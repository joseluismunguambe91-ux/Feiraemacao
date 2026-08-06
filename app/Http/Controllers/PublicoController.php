<?php

namespace App\Http\Controllers;

use App\Http\Requests\Publico\ContactoRequest;
use App\Models\Feira;
use App\Models\MensagemContacto;
use App\Models\ProgramacaoItem;
use App\Models\Stand;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * RF28/RF29 (Etapa 1): vitrine da feira sem login, sempre a refletir a
 * edição publicada/em_curso (RN10) — nunca uma edição em rascunho.
 */
class PublicoController extends Controller
{
    public function inicio(): View
    {
        $feira = $this->feiraPublica();
        $destaques = collect();

        if ($feira) {
            $dataDestaque = now()->between($feira->data_inicio, $feira->data_fim)
                ? now()->toDateString()
                : $feira->data_inicio->format('Y-m-d');

            $destaques = ProgramacaoItem::where('feira_id', $feira->id)
                ->whereDate('data', $dataDestaque)
                ->orderBy('hora_inicio')
                ->with('atividade')
                ->limit(3)
                ->get();
        }

        return view('publico.inicio', [
            'feira' => $feira,
            'destaques' => $destaques,
            'totalExpositores' => $feira?->expositores()->where('estado', 'ativo')->count() ?? 0,
            'totalStands' => $feira?->stands()->whereHas('expositor')->count() ?? 0,
            'totalAtividades' => $feira?->atividades()->where('estado', '!=', 'cancelada')->count() ?? 0,
        ]);
    }

    public function sobre(): View
    {
        return view('publico.sobre', ['feira' => $this->feiraPublica()]);
    }

    public function programacao(): View
    {
        $feira = $this->feiraPublica();

        $itens = $feira
            ? ProgramacaoItem::where('feira_id', $feira->id)->with('atividade')->orderBy('data')->orderBy('hora_inicio')->get()
            : collect();

        return view('publico.programacao', compact('feira', 'itens'));
    }

    public function atividades(): View
    {
        $feira = $this->feiraPublica();

        $atividades = $feira
            ? $feira->atividades()->where('estado', '!=', 'cancelada')->orderBy('titulo')->get()
            : collect();

        return view('publico.atividades', compact('feira', 'atividades'));
    }

    public function gastronomia(): View
    {
        $feira = $this->feiraPublica();

        $itens = $feira
            ? $feira->gastronomiaItens()->with(['inscricao.alunos', 'inscricao.professor'])->orderBy('categoria')->orderBy('nome')->get()
            : collect();

        return view('publico.gastronomia', compact('feira', 'itens'));
    }

    public function expositores(): View
    {
        $feira = $this->feiraPublica();

        $expositores = $feira
            ? $feira->expositores()->where('estado', 'ativo')->with('stand')->orderBy('turma')->get()
            : collect();

        return view('publico.expositores', compact('feira', 'expositores'));
    }

    public function mapa(): View
    {
        $feira = $this->feiraPublica();

        $stands = $feira
            ? $feira->stands()->whereHas('expositor')->with('expositor')->orderBy('numero')->get()
            : collect();

        return view('publico.mapa', compact('feira', 'stands'));
    }

    public function stand(string $qrToken): View
    {
        $stand = Stand::where('qr_token', $qrToken)->with(['expositor.fotos', 'feira'])->firstOrFail();

        return view('publico.stand', compact('stand'));
    }

    public function galeria(): View
    {
        $feira = $this->feiraPublica();

        $itens = $feira
            ? $feira->galeriaItens()->orderBy('categoria')->orderBy('ordem')->get()
            : collect();

        return view('publico.galeria', compact('feira', 'itens'));
    }

    public function patrocinadores(): View
    {
        $feira = $this->feiraPublica();

        $patrocinadores = $feira
            ? $feira->patrocinadores()->orderBy('ordem')->get()
            : collect();

        return view('publico.patrocinadores', compact('feira', 'patrocinadores'));
    }

    public function pesquisa(Request $request): View
    {
        $termo = trim((string) $request->query('q', ''));
        $feira = $this->feiraPublica();

        $resultados = [
            'atividades' => collect(),
            'gastronomia' => collect(),
            'expositores' => collect(),
            'stands' => collect(),
            'professores' => collect(),
        ];

        if ($feira && $termo !== '') {
            $like = '%'.$termo.'%';

            $resultados['atividades'] = $feira->atividades()
                ->where(fn ($q) => $q->where('titulo', 'like', $like)->orWhere('descricao', 'like', $like))
                ->limit(10)->get();

            $resultados['gastronomia'] = $feira->gastronomiaItens()
                ->where('nome', 'like', $like)
                ->limit(10)->get();

            $resultados['expositores'] = $feira->expositores()
                ->where(fn ($q) => $q->where('turma', 'like', $like)->orWhere('categoria', 'like', $like))
                ->with('professor')->limit(10)->get();

            $resultados['stands'] = $feira->stands()
                ->where(fn ($q) => $q->where('numero', 'like', $like)->orWhere('localizacao', 'like', $like))
                ->limit(10)->get();

            $resultados['professores'] = User::whereHas('roles', fn ($q) => $q->where('slug', 'professor'))
                ->where('name', 'like', $like)
                ->limit(10)->get();
        }

        return view('publico.pesquisa', ['feira' => $feira, 'termo' => $termo, 'resultados' => $resultados]);
    }

    public function contacto(): View
    {
        return view('publico.contacto', ['feira' => $this->feiraPublica()]);
    }

    public function contactoStore(ContactoRequest $request): RedirectResponse
    {
        MensagemContacto::create([
            ...$request->validated(),
            'feira_id' => $this->feiraPublica()?->id,
        ]);

        return redirect()->route('publico.contacto')->with('sucesso', 'Mensagem enviada. Obrigado pelo contacto!');
    }

    private function feiraPublica(): ?Feira
    {
        return Feira::ativa()->first();
    }
}
