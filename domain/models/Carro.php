<?php

namespace Domain\Models;

use DateTime;

class Carro extends Veiculo
{
    private const TIPO = 'carro';
    
    public function obterTipo(): string
    {
        return self::TIPO;
    }
}
