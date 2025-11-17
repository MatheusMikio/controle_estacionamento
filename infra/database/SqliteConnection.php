<?php

namespace Infra\Database;

use Domain\Interfaces\IDatabaseConnection;
use PDO;
use PDOException;

class SqliteConnection implements IDatabaseConnection
{
    private const DB_PATH = __DIR__ . '/../../database/estacionamento.db';
    private ?PDO $pdo = null;
    
    public function connect(): PDO
    {
        if ($this->pdo !== null) {
            return $this->pdo;
        }
        
        try {
            $this->criarDiretorioSeNaoExistir();
            
            $this->pdo = new PDO(
                'sqlite:' . self::DB_PATH,
                null,
                null,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );
            
            $this->inicializarTabelas();
            
            return $this->pdo;
        } catch (PDOException $e) {
            throw new \RuntimeException('Erro ao conectar ao banco de dados: ' . $e->getMessage());
        }
    }
    
    public function inicializarTabelas(): void
    {
        $sql = <<<SQL
        CREATE TABLE IF NOT EXISTS veiculos (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            placa VARCHAR(10) NOT NULL,
            tipo VARCHAR(20) NOT NULL,
            data_hora_entrada DATETIME NOT NULL,
            data_hora_saida DATETIME,
            ativo BOOLEAN NOT NULL DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );
        
        CREATE INDEX IF NOT EXISTS idx_placa ON veiculos(placa);
        CREATE INDEX IF NOT EXISTS idx_ativo ON veiculos(ativo);
        SQL;
        
        $this->connect()->exec($sql);
    }
    
    private function criarDiretorioSeNaoExistir(): void
    {
        $dir = dirname(self::DB_PATH);
        
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }
}
