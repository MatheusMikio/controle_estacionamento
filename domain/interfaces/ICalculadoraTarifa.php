<?php

namespace Domain\Interfaces;

use Domain\Models\Veiculo;

interface ICalculadoraTarifa
{
    public function calcular(Veiculo $veiculo): float;
}
