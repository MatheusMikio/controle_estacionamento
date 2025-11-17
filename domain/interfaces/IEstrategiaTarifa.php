<?php

namespace Domain\Interfaces;

interface IEstrategiaTarifa
{
    public function calcularTarifa(int $horasEstacionado): float;
    
    public function obterValorHora(): float;
    
    public function obterTipoVeiculo(): string;
}
