<?php

namespace Infra\Repository;

use Domain\Interfaces\IVeiculoRepository;
use Domain\Interfaces\IDatabaseConnection;
use Domain\Models\Veiculo;
use Domain\Models\FabricaVeiculo;
use PDO;
use DateTime;

class VeiculoRepository implements IVeiculoRepository
{
    private PDO $pdo;
    private FabricaVeiculo $fabricaVeiculo;
    
    public function __construct(IDatabaseConnection $connection, FabricaVeiculo $fabricaVeiculo)
    {
        $this->pdo = $connection->connect();
        $this->fabricaVeiculo = $fabricaVeiculo;
    }
    
    public function salvar(Veiculo $veiculo): bool
    {
        $sql = <<<SQL
        INSERT INTO veiculos (placa, tipo, data_hora_entrada, data_hora_saida, ativo)
        VALUES (:placa, :tipo, :data_hora_entrada, :data_hora_saida, :ativo)
        SQL;
        
        $stmt = $this->pdo->prepare($sql);
        
        $resultado = $stmt->execute([
            'placa' => $veiculo->getPlaca(),
            'tipo' => $veiculo->obterTipo(),
            'data_hora_entrada' => $veiculo->getDataHoraEntrada()->format('Y-m-d H:i:s'),
            'data_hora_saida' => $veiculo->getDataHoraSaida()?->format('Y-m-d H:i:s'),
            'ativo' => $veiculo->estaAtivo() ? 1 : 0,
        ]);
        
        if ($resultado) {
            $veiculo->setId((int) $this->pdo->lastInsertId());
        }
        
        return $resultado;
    }
    
    public function buscarPorId(int $id): ?Veiculo
    {
        $sql = 'SELECT * FROM veiculos WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        
        $dados = $stmt->fetch();
        
        if (!$dados) {
            return null;
        }
        
        return $this->criarVeiculoDeDados($dados);
    }
    
    public function buscarPorPlaca(string $placa): ?Veiculo
    {
        $sql = 'SELECT * FROM veiculos WHERE placa = :placa AND ativo = 1 ORDER BY id DESC LIMIT 1';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['placa' => strtoupper($placa)]);
        
        $dados = $stmt->fetch();
        
        if (!$dados) {
            return null;
        }
        
        return $this->criarVeiculoDeDados($dados);
    }
    
    public function listarAtivos(): array
    {
        $sql = 'SELECT * FROM veiculos WHERE ativo = 1 ORDER BY data_hora_entrada DESC';
        $stmt = $this->pdo->query($sql);
        
        return $this->criarVeiculosDeDados($stmt->fetchAll());
    }
    
    public function listarTodos(): array
    {
        $sql = 'SELECT * FROM veiculos ORDER BY data_hora_entrada DESC';
        $stmt = $this->pdo->query($sql);
        
        return $this->criarVeiculosDeDados($stmt->fetchAll());
    }
    
    public function atualizar(Veiculo $veiculo): bool
    {
        $sql = <<<SQL
        UPDATE veiculos 
        SET data_hora_saida = :data_hora_saida, ativo = :ativo
        WHERE id = :id
        SQL;
        
        $stmt = $this->pdo->prepare($sql);
        
        return $stmt->execute([
            'id' => $veiculo->getId(),
            'data_hora_saida' => $veiculo->getDataHoraSaida()?->format('Y-m-d H:i:s'),
            'ativo' => $veiculo->estaAtivo() ? 1 : 0,
        ]);
    }
    
    private function criarVeiculoDeDados(array $dados): Veiculo
    {
        return Veiculo::reconstruir(
            $dados['placa'],
            $dados['tipo'],
            (int) $dados['id'],
            new DateTime($dados['data_hora_entrada']),
            $dados['data_hora_saida'] ? new DateTime($dados['data_hora_saida']) : null,
            (bool) $dados['ativo']
        );
    }
    
    private function criarVeiculosDeDados(array $registros): array
    {
        return array_map(
            fn($dados) => $this->criarVeiculoDeDados($dados),
            $registros
        );
    }
}
