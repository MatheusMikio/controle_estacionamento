<?php

namespace Domain\Models;

class FabricaVeiculo
{
    private const TIPOS_VALIDOS = ['carro', 'moto', 'caminhao'];
    
    public function criar(string $tipo, string $placa): Veiculo
    {
        $tipoNormalizado = strtolower(trim($tipo));
        
        if (!in_array($tipoNormalizado, self::TIPOS_VALIDOS)) {
            throw new \InvalidArgumentException("Tipo de veículo inválido: {$tipo}");
        }
        
        return match ($tipoNormalizado) {
            'carro' => new Carro($placa),
            'moto' => new Moto($placa),
            'caminhao' => new Caminhao($placa),
        };
    }
    
    public function obterTiposValidos(): array
    {
        return self::TIPOS_VALIDOS;
    }
}
