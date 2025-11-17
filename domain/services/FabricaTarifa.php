<?php

namespace Domain\Services;

use Domain\Interfaces\IEstrategiaTarifa;

class FabricaTarifa
{
    private array $estrategias = [];
    
    public function __construct()
    {
        $this->registrarEstrategia(new TarifaCarro());
        $this->registrarEstrategia(new TarifaMoto());
        $this->registrarEstrategia(new TarifaCaminhao());
    }
    
    public function obterEstrategia(string $tipoVeiculo): IEstrategiaTarifa
    {
        $tipo = strtolower(trim($tipoVeiculo));
        
        if (!isset($this->estrategias[$tipo])) {
            throw new \InvalidArgumentException("Tipo de veículo não possui estratégia de tarifa: {$tipoVeiculo}");
        }
        
        return $this->estrategias[$tipo];
    }
    
    private function registrarEstrategia(IEstrategiaTarifa $estrategia): void
    {
        $this->estrategias[$estrategia->obterTipoVeiculo()] = $estrategia;
    }
    
    public function obterTodasEstrategias(): array
    {
        return $this->estrategias;
    }
}
