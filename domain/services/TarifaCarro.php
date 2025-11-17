<?php

namespace Domain\Services;

use Domain\Interfaces\IEstrategiaTarifa;

class TarifaCarro implements IEstrategiaTarifa
{
    private const VALOR_HORA = 5.00;
    private const TIPO = 'carro';
    
    public function calcularTarifa(int $horasEstacionado): float
    {
        return $horasEstacionado * self::VALOR_HORA;
    }
    
    public function obterValorHora(): float
    {
        return self::VALOR_HORA;
    }
    
    public function obterTipoVeiculo(): string
    {
        return self::TIPO;
    }
}
