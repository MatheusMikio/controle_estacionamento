<?php

namespace Domain\Services;

use Domain\Interfaces\ICalculadoraTarifa;
use Domain\Models\Veiculo;

class CalculadoraTarifa implements ICalculadoraTarifa
{
    private FabricaTarifa $fabricaTarifa;
    
    public function __construct(FabricaTarifa $fabricaTarifa)
    {
        $this->fabricaTarifa = $fabricaTarifa;
    }
    
    public function calcular(Veiculo $veiculo): float
    {
        $estrategia = $this->fabricaTarifa->obterEstrategia($veiculo->obterTipo());
        $horas = $veiculo->calcularHorasEstacionado();
        
        return $estrategia->calcularTarifa($horas);
    }
}
