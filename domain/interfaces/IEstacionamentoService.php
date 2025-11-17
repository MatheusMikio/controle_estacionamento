<?php

namespace Domain\Interfaces;

interface IEstacionamentoService
{
    public function registrarEntrada(string $placa, string $tipoVeiculo): array;
    
    public function registrarSaida(string $placa): array;
    
    public function listarVeiculosEstacionados(): array;
}
