<?php

namespace Domain\Services;

use Domain\Interfaces\IEstacionamentoService;
use Domain\Interfaces\IVeiculoRepository;
use Domain\Interfaces\ICalculadoraTarifa;
use Domain\Models\FabricaVeiculo;

class EstacionamentoService implements IEstacionamentoService
{
    private IVeiculoRepository $veiculoRepository;
    private FabricaVeiculo $fabricaVeiculo;
    private ICalculadoraTarifa $calculadoraTarifa;
    
    public function __construct(
        IVeiculoRepository $veiculoRepository,
        FabricaVeiculo $fabricaVeiculo,
        ICalculadoraTarifa $calculadoraTarifa
    ) {
        $this->veiculoRepository = $veiculoRepository;
        $this->fabricaVeiculo = $fabricaVeiculo;
        $this->calculadoraTarifa = $calculadoraTarifa;
    }
    
    public function registrarEntrada(string $placa, string $tipoVeiculo): array
    {
        try {
            $veiculoExistente = $this->veiculoRepository->buscarPorPlaca($placa);
            
            if ($veiculoExistente !== null && $veiculoExistente->estaAtivo()) {
                return [
                    'sucesso' => false,
                    'mensagem' => 'Veículo já está estacionado',
                ];
            }
            
            $veiculo = $this->fabricaVeiculo->criar($tipoVeiculo, $placa);
            $this->veiculoRepository->salvar($veiculo);
            
            return [
                'sucesso' => true,
                'mensagem' => 'Entrada registrada com sucesso',
                'veiculo' => $veiculo->toArray(),
            ];
        } catch (\Exception $e) {
            return [
                'sucesso' => false,
                'mensagem' => $e->getMessage(),
            ];
        }
    }
    
    public function registrarSaida(string $placa): array
    {
        try {
            $veiculo = $this->veiculoRepository->buscarPorPlaca($placa);
            
            if ($veiculo === null) {
                return [
                    'sucesso' => false,
                    'mensagem' => 'Veículo não encontrado',
                ];
            }
            
            if (!$veiculo->estaAtivo()) {
                return [
                    'sucesso' => false,
                    'mensagem' => 'Veículo já teve saída registrada',
                ];
            }
            
            $veiculo->registrarSaida();
            $tarifa = $this->calculadoraTarifa->calcular($veiculo);
            $this->veiculoRepository->atualizar($veiculo);
            
            return [
                'sucesso' => true,
                'mensagem' => 'Saída registrada com sucesso',
                'veiculo' => $veiculo->toArray(),
                'horas' => $veiculo->calcularHorasEstacionado(),
                'tarifa' => $tarifa,
            ];
        } catch (\Exception $e) {
            return [
                'sucesso' => false,
                'mensagem' => $e->getMessage(),
            ];
        }
    }
    
    public function listarVeiculosEstacionados(): array
    {
        try {
            $veiculos = $this->veiculoRepository->listarAtivos();
            
            return [
                'sucesso' => true,
                'veiculos' => array_map(fn($v) => $v->toArray(), $veiculos),
            ];
        } catch (\Exception $e) {
            return [
                'sucesso' => false,
                'mensagem' => $e->getMessage(),
            ];
        }
    }
}
