<?php

namespace Domain\Services;

use Domain\Interfaces\IGeraRelatorioVeiculos;
use Domain\Interfaces\IVeiculoRepository;
use Domain\Interfaces\ICalculadoraTarifa;
use Domain\Services\FabricaTarifa;

class RelatorioService implements IGeraRelatorioVeiculos
{
    private IVeiculoRepository $veiculoRepository;
    private ICalculadoraTarifa $calculadoraTarifa;
    private FabricaTarifa $fabricaTarifa;
    
    public function __construct(
        IVeiculoRepository $veiculoRepository,
        ICalculadoraTarifa $calculadoraTarifa,
        FabricaTarifa $fabricaTarifa
    ) {
        $this->veiculoRepository = $veiculoRepository;
        $this->calculadoraTarifa = $calculadoraTarifa;
        $this->fabricaTarifa = $fabricaTarifa;
    }
    
    public function gerarRelatorio(): array
    {
        $veiculos = $this->veiculoRepository->listarTodos();
        
        $relatorio = [
            'total_geral' => 0,
            'faturamento_total' => 0.0,
            'por_tipo' => [],
        ];
        
        foreach ($this->fabricaTarifa->obterTodasEstrategias() as $tipo => $estrategia) {
            $relatorio['por_tipo'][$tipo] = [
                'tipo' => $tipo,
                'quantidade' => 0,
                'faturamento' => 0.0,
                'valor_hora' => $estrategia->obterValorHora(),
            ];
        }
        
        foreach ($veiculos as $veiculo) {
            $tipo = $veiculo->obterTipo();
            $relatorio['total_geral']++;
            $relatorio['por_tipo'][$tipo]['quantidade']++;
            
            if (!$veiculo->estaAtivo()) {
                $tarifa = $this->calculadoraTarifa->calcular($veiculo);
                $relatorio['por_tipo'][$tipo]['faturamento'] += $tarifa;
                $relatorio['faturamento_total'] += $tarifa;
            }
        }
        
        return $relatorio;
    }
}
