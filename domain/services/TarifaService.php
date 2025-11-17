<?php

namespace Application\Service;

use Domain\Interfaces\ITarifaService;
use Domain\Services\FabricaTarifa;

class TarifaService implements ITarifaService
{
    private FabricaTarifa $fabricaTarifa;
    
    public function __construct(FabricaTarifa $fabricaTarifa)
    {
        $this->fabricaTarifa = $fabricaTarifa;
    }
    
    public function calcularTarifa(string $tipoVeiculo, int $horasEstacionado): float
    {
        $estrategia = $this->fabricaTarifa->obterEstrategia($tipoVeiculo);
        
        return $estrategia->calcularTarifa($horasEstacionado);
    }
    
    public function obterTarifaPorTipo(string $tipoVeiculo): float
    {
        $estrategia = $this->fabricaTarifa->obterEstrategia($tipoVeiculo);
        
        return $estrategia->obterValorHora();
    }
}
