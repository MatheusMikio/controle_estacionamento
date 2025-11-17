<?php

namespace Domain\Interfaces;

interface IDatabaseConnection
{
    public function connect(): \PDO;
    
    public function inicializarTabelas(): void;
}
