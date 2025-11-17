<?php

namespace Domain\Models;

use DateTime;

abstract class Veiculo
{
    protected ?int $id = null;
    protected string $placa;
    protected DateTime $dataHoraEntrada;
    protected ?DateTime $dataHoraSaida = null;
    protected bool $ativo;
    
    public function __construct(string $placa, ?DateTime $dataHoraEntrada = null)
    {
        $this->validarPlaca($placa);
        $this->placa = strtoupper($placa);
        $this->dataHoraEntrada = $dataHoraEntrada ?? new DateTime();
        $this->ativo = true;
    }
    
    public static function reconstruir(
        string $placa,
        string $tipo,
        int $id,
        DateTime $dataHoraEntrada,
        ?DateTime $dataHoraSaida,
        bool $ativo
    ): self {
        $classe = match (strtolower($tipo)) {
            'carro' => Carro::class,
            'moto' => Moto::class,
            'caminhao' => Caminhao::class,
            default => throw new \InvalidArgumentException("Tipo inválido: {$tipo}"),
        };
        
        $veiculo = new $classe($placa, $dataHoraEntrada);
        $veiculo->id = $id;
        $veiculo->dataHoraSaida = $dataHoraSaida;
        $veiculo->ativo = $ativo;
        
        return $veiculo;
    }
    
    abstract public function obterTipo(): string;
    
    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function setId(int $id): void
    {
        $this->id = $id;
    }
    
    public function getPlaca(): string
    {
        return $this->placa;
    }
    
    public function getDataHoraEntrada(): DateTime
    {
        return $this->dataHoraEntrada;
    }
    
    public function getDataHoraSaida(): ?DateTime
    {
        return $this->dataHoraSaida;
    }
    
    public function registrarSaida(?DateTime $dataHoraSaida = null): void
    {
        $this->dataHoraSaida = $dataHoraSaida ?? new DateTime();
        $this->ativo = false;
    }
    
    public function estaAtivo(): bool
    {
        return $this->ativo;
    }
    
    public function setAtivo(bool $ativo): void
    {
        $this->ativo = $ativo;
    }
    
    public function calcularHorasEstacionado(): int
    {
        $dataFinal = $this->dataHoraSaida ?? new DateTime();
        $intervalo = $this->dataHoraEntrada->diff($dataFinal);
        
        $horas = $intervalo->h + ($intervalo->days * 24);
        
        return $this->arredondarParaCima($horas, $intervalo->i);
    }
    
    private function arredondarParaCima(int $horas, int $minutos): int
    {
        if ($minutos > 0) {
            return $horas + 1;
        }
        
        return max(1, $horas);
    }
    
    private function validarPlaca(string $placa): void
    {
        $placaLimpa = preg_replace('/[^A-Z0-9]/i', '', $placa);
        
        if (strlen($placaLimpa) < 6 || strlen($placaLimpa) > 7) {
            throw new \InvalidArgumentException('Placa inválida');
        }
    }
    
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'placa' => $this->placa,
            'tipo' => $this->obterTipo(),
            'dataHoraEntrada' => $this->dataHoraEntrada->format('Y-m-d H:i:s'),
            'dataHoraSaida' => $this->dataHoraSaida?->format('Y-m-d H:i:s'),
            'ativo' => $this->ativo,
        ];
    }
}
