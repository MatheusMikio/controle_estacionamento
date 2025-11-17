<?php

namespace Domain\Interfaces;

interface ITarifaService
{
    public function calcularTarifa(string $tipoVeiculo, int $horasEstacionado): float;
    
    public function obterTarifaPorTipo(string $tipoVeiculo): float;
}
