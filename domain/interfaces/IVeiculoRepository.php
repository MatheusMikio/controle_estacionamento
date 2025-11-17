<?php

namespace Domain\Interfaces;

use Domain\Models\Veiculo;

interface IVeiculoRepository
{
    public function salvar(Veiculo $veiculo): bool;
    
    public function buscarPorId(int $id): ?Veiculo;
    
    public function buscarPorPlaca(string $placa): ?Veiculo;
    
    public function listarAtivos(): array;
    
    public function listarTodos(): array;
    
    public function atualizar(Veiculo $veiculo): bool;
}
