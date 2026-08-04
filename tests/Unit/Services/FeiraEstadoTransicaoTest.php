<?php

namespace Tests\Unit\Services;

use App\Models\Feira;
use App\Services\FeiraEstadoTransicao;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

/**
 * RF07/RF08 (Etapa 1) e RN02 (Etapa 3): o ciclo de vida da feira e a
 * garantia de que só uma edição está ativa de cada vez.
 */
class FeiraEstadoTransicaoTest extends TestCase
{
    use RefreshDatabase;

    private FeiraEstadoTransicao $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new FeiraEstadoTransicao();
    }

    public function test_avanca_pela_sequencia_completa_do_estado(): void
    {
        $feira = Feira::factory()->create(['estado' => 'rascunho']);

        $this->service->avancar($feira);
        $this->assertSame('publicada', $feira->fresh()->estado);

        $this->service->avancar($feira);
        $this->assertSame('em_curso', $feira->fresh()->estado);

        $this->service->avancar($feira);
        $this->assertSame('encerrada', $feira->fresh()->estado);

        $this->service->avancar($feira);
        $this->assertSame('arquivada', $feira->fresh()->estado);
    }

    public function test_nao_avanca_para_alem_de_arquivada(): void
    {
        $feira = Feira::factory()->arquivada()->create();

        $this->expectException(RuntimeException::class);
        $this->service->avancar($feira);
    }

    public function test_bloqueia_publicar_quando_ja_existe_outra_feira_ativa(): void
    {
        Feira::factory()->publicada()->create();
        $candidata = Feira::factory()->create(['estado' => 'rascunho']);

        $this->expectException(RuntimeException::class);
        $this->service->avancar($candidata);
    }

    public function test_reverte_um_passo_incluindo_desarquivar(): void
    {
        $feira = Feira::factory()->arquivada()->create();

        $this->service->reverter($feira);

        $this->assertSame('encerrada', $feira->fresh()->estado);
    }

    public function test_nao_reverte_antes_de_rascunho(): void
    {
        $feira = Feira::factory()->create(['estado' => 'rascunho']);

        $this->expectException(RuntimeException::class);
        $this->service->reverter($feira);
    }
}
