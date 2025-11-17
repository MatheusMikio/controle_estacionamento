<?php

namespace Domain\Models;

class Caminhao extends Veiculo
{
    private const TIPO = 'caminhao';
    
    public function obterTipo(): string
    {
        return self::TIPO;
    }
}
