<?php

namespace Domain\Models;

use DateTime;

class Moto extends Veiculo
{
    private const TIPO = 'moto';
    
    public function obterTipo(): string
    {
        return self::TIPO;
    }
}
