<?php

date_default_timezone_set('America/Sao_Paulo');

require_once __DIR__ . '/../vendor/autoload.php';

use Infra\Database\SqliteConnection;
use Infra\Repository\VeiculoRepository;
use Domain\Models\FabricaVeiculo;
use Domain\Services\FabricaTarifa;
use Domain\Services\CalculadoraTarifa;
use Domain\Services\EstacionamentoService;
use Domain\Services\RelatorioService;

$connection = new SqliteConnection();
$fabricaVeiculo = new FabricaVeiculo();
$fabricaTarifa = new FabricaTarifa();
$calculadoraTarifa = new CalculadoraTarifa($fabricaTarifa);
$veiculoRepository = new VeiculoRepository($connection, $fabricaVeiculo);

$estacionamentoService = new EstacionamentoService(
    $veiculoRepository,
    $fabricaVeiculo,
    $calculadoraTarifa
);

$relatorioService = new RelatorioService(
    $veiculoRepository,
    $calculadoraTarifa,
    $fabricaTarifa
);

$acao = $_POST['acao'] ?? $_GET['acao'] ?? null;

if ($acao === 'registrar_entrada') {
    header('Content-Type: application/json');
    $placa = $_POST['placa'] ?? '';
    $tipo = $_POST['tipo'] ?? '';
    echo json_encode($estacionamentoService->registrarEntrada($placa, $tipo));
    exit;
}

if ($acao === 'registrar_saida') {
    header('Content-Type: application/json');
    $placa = $_POST['placa'] ?? '';
    echo json_encode($estacionamentoService->registrarSaida($placa));
    exit;
}

if ($acao === 'listar_veiculos') {
    header('Content-Type: application/json');
    echo json_encode($estacionamentoService->listarVeiculosEstacionados());
    exit;
}

if ($acao === 'gerar_relatorio') {
    header('Content-Type: application/json');
    echo json_encode($relatorioService->gerarRelatorio());
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Estacionamento</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    
    <header class="gradient-bg text-white shadow-lg">
        <div class="container mx-auto px-4 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <div>
                        <h1 class="text-3xl font-bold">Sistema de Estacionamento</h1>
                        <p class="text-sm text-purple-200">Controle inteligente de veículos</p>
                    </div>
                </div>
                <div class="text-right">
                    <div id="relogio" class="text-xl font-semibold"></div>
                </div>
            </div>
        </div>
    </header>

    <div class="container mx-auto px-4 py-8">
        <div class="mb-8">
            <div class="bg-white rounded-xl shadow-md p-2">
                <nav class="flex flex-wrap gap-2">
                    <button onclick="mostrarAba('entrada')" class="tab-button flex-1 min-w-[200px] px-6 py-4 font-medium rounded-lg transition-all duration-200" id="tab-entrada">
                        <div class="flex items-center justify-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                            </svg>
                            <span>Entrada</span>
                        </div>
                    </button>
                    <button onclick="mostrarAba('saida')" class="tab-button flex-1 min-w-[200px] px-6 py-4 font-medium rounded-lg transition-all duration-200" id="tab-saida">
                        <div class="flex items-center justify-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            <span>Saída</span>
                        </div>
                    </button>
                    <button onclick="mostrarAba('listar')" class="tab-button flex-1 min-w-[200px] px-6 py-4 font-medium rounded-lg transition-all duration-200" id="tab-listar">
                        <div class="flex items-center justify-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <span>Estacionados</span>
                        </div>
                    </button>
                    <button onclick="mostrarAba('relatorio')" class="tab-button flex-1 min-w-[200px] px-6 py-4 font-medium rounded-lg transition-all duration-200" id="tab-relatorio">
                        <div class="flex items-center justify-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            <span>Relatório</span>
                        </div>
                    </button>
                </nav>
            </div>
        </div>

        <div id="aba-entrada" class="aba hidden fade-in">
            <div class="max-w-2xl mx-auto">
                <div class="bg-white rounded-2xl shadow-xl p-8 card-hover">
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="p-3 bg-blue-100 rounded-full">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </div>
                        <h2 class="text-3xl font-bold text-gray-800">Registrar Entrada</h2>
                    </div>
                    <form id="form-entrada" class="space-y-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Placa do Veículo</label>
                            <input type="text" id="placa-entrada" name="placa" required maxlength="8" 
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition uppercase text-lg font-mono" 
                                placeholder="ABC-1234">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Tipo de Veículo</label>
                            <div class="grid grid-cols-3 gap-4">
                                <label class="relative cursor-pointer radio-card">
                                    <input type="radio" name="tipo" value="carro" required class="peer sr-only">
                                    <div class="p-4 border-2 border-gray-200 rounded-xl peer-checked:border-blue-500 peer-checked:bg-blue-50 transition text-center">
                                        <svg class="w-12 h-12 mx-auto mb-2 text-blue-500 svg-icon svg-icon-car" fill="currentColor" viewBox="0 0 64 64">
                                            <path d="M58,30h-4.5l-6-10c-0.8-1.3-2.2-2-3.7-2H20.2c-1.5,0-2.9,0.7-3.7,2l-6,10H6c-2.2,0-4,1.8-4,4v16c0,2.2,1.8,4,4,4h4v4c0,1.1,0.9,2,2,2h4c1.1,0,2-0.9,2-2v-4h28v4c0,1.1,0.9,2,2,2h4c1.1,0,2-0.9,2-2v-4h4c2.2,0,4-1.8,4-4V34C62,31.8,60.2,30,58,30z M18.8,22h26.4l4.5,8H14.3L18.8,22z M14,46c-2.2,0-4-1.8-4-4s1.8-4,4-4s4,1.8,4,4S16.2,46,14,46z M50,46c-2.2,0-4-1.8-4-4s1.8-4,4-4s4,1.8,4,4S52.2,46,50,46z"/>
                                            <circle cx="14" cy="42" r="2" opacity="0.3"/>
                                            <circle cx="50" cy="42" r="2" opacity="0.3"/>
                                        </svg>
                                        <div class="font-semibold text-gray-700">Carro</div>
                                        <div class="text-sm text-gray-500">R$ 5,00/h</div>
                                    </div>
                                </label>
                                <label class="relative cursor-pointer radio-card">
                                    <input type="radio" name="tipo" value="moto" required class="peer sr-only">
                                    <div class="p-4 border-2 border-gray-200 rounded-xl peer-checked:border-purple-500 peer-checked:bg-purple-50 transition text-center">
                                        <svg class="w-12 h-12 mx-auto mb-2 text-purple-500 svg-icon svg-icon-moto" fill="currentColor" viewBox="0 0 64 64">
                                            <circle cx="14" cy="48" r="10" stroke="#fff" stroke-width="2"/>
                                            <circle cx="50" cy="48" r="10" stroke="#fff" stroke-width="2"/>
                                            <path d="M14,38c-5.5,0-10,4.5-10,10s4.5,10,10,10s10-4.5,10-10S19.5,38,14,38z M14,54c-3.3,0-6-2.7-6-6s2.7-6,6-6s6,2.7,6,6S17.3,54,14,54z"/>
                                            <path d="M50,38c-5.5,0-10,4.5-10,10s4.5,10,10,10s10-4.5,10-10S55.5,38,50,38z M50,54c-3.3,0-6-2.7-6-6s2.7-6,6-6s6,2.7,6,6S53.3,54,50,54z"/>
                                            <path d="M42,18h-8l-4,8h-6l-2-4h-6c-1.1,0-2,0.9-2,2v4c0,1.1,0.9,2,2,2h4l8,18h8l4-8l8,8h4V30l-8-12H42z"/>
                                            <rect x="38" y="14" width="8" height="4" rx="2"/>
                                            <circle cx="14" cy="48" r="3" opacity="0.3"/>
                                            <circle cx="50" cy="48" r="3" opacity="0.3"/>
                                        </svg>
                                        <div class="font-semibold text-gray-700">Moto</div>
                                        <div class="text-sm text-gray-500">R$ 3,00/h</div>
                                    </div>
                                </label>
                                <label class="relative cursor-pointer radio-card">
                                    <input type="radio" name="tipo" value="caminhao" required class="peer sr-only">
                                    <div class="p-4 border-2 border-gray-200 rounded-xl peer-checked:border-orange-500 peer-checked:bg-orange-50 transition text-center">
                                        <svg class="w-12 h-12 mx-auto mb-2 text-orange-500 svg-icon svg-icon-truck" fill="currentColor" viewBox="0 0 64 64">
                                            <path d="M58,30h-6V20c0-2.2-1.8-4-4-4H6c-2.2,0-4,1.8-4,4v22c0,2.2,1.8,4,4,4h2c0,4.4,3.6,8,8,8s8-3.6,8-8h16c0,4.4,3.6,8,8,8s8-3.6,8-8h2c2.2,0,4-1.8,4-4V34C62,31.8,60.2,30,58,30z M16,50c-2.2,0-4-1.8-4-4s1.8-4,4-4s4,1.8,4,4S18.2,50,16,50z M48,50c-2.2,0-4-1.8-4-4s1.8-4,4-4s4,1.8,4,4S50.2,50,48,50z M52,38h-4V30h4l6,6v2H52z"/>
                                            <rect x="8" y="20" width="6" height="8" rx="1" opacity="0.3"/>
                                            <rect x="16" y="20" width="10" height="8" rx="1" opacity="0.3"/>
                                            <rect x="28" y="20" width="6" height="8" rx="1" opacity="0.3"/>
                                            <circle cx="16" cy="46" r="2" opacity="0.3"/>
                                            <circle cx="48" cy="46" r="2" opacity="0.3"/>
                                        </svg>
                                        <div class="font-semibold text-gray-700">Caminhão</div>
                                        <div class="text-sm text-gray-500">R$ 10,00/h</div>
                                    </div>
                                </label>
                            </div>
                        </div>
                        <button type="submit" class="w-full bg-gradient-to-r from-blue-500 to-blue-600 text-white px-6 py-4 rounded-xl hover:from-blue-600 hover:to-blue-700 transition shadow-lg hover:shadow-xl font-semibold text-lg">
                            ✓ Registrar Entrada
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div id="aba-saida" class="aba hidden fade-in">
            <div class="max-w-2xl mx-auto">
                <div class="bg-white rounded-2xl shadow-xl p-8 card-hover">
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="p-3 bg-green-100 rounded-full">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <h2 class="text-3xl font-bold text-gray-800">Registrar Saída</h2>
                    </div>
                    <form id="form-saida" class="space-y-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Placa do Veículo</label>
                            <input type="text" name="placa" required maxlength="8"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition uppercase text-lg font-mono" 
                                placeholder="ABC-1234">
                        </div>
                        <button type="submit" class="w-full bg-gradient-to-r from-green-500 to-green-600 text-white px-6 py-4 rounded-xl hover:from-green-600 hover:to-green-700 transition shadow-lg hover:shadow-xl font-semibold text-lg">
                            Registrar Saída e Calcular
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div id="aba-listar" class="aba hidden fade-in">
            <div class="bg-white rounded-2xl shadow-xl p-8">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center space-x-3">
                        <div class="p-3 bg-purple-100 rounded-full">
                            <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </div>
                        <h2 class="text-3xl font-bold text-gray-800">Veículos Estacionados</h2>
                    </div>
                </div>
                <div id="lista-veiculos" class="overflow-x-auto"></div>
            </div>
        </div>

        <div id="aba-relatorio" class="aba hidden fade-in">
            <div class="bg-white rounded-2xl shadow-xl p-8">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center space-x-3">
                        <div class="p-3 bg-orange-100 rounded-full">
                            <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <h2 class="text-3xl font-bold text-gray-800">Relatório Financeiro</h2>
                    </div>
                </div>
                <div id="conteudo-relatorio"></div>
            </div>
        </div>
    </div>

    <script>
        function mostrarAba(aba) {
            document.querySelectorAll('.aba').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('.tab-button').forEach(el => {
                el.classList.remove('border-blue-500', 'text-blue-600');
                el.classList.add('border-transparent', 'text-gray-600');
            });
            
            document.getElementById('aba-' + aba).classList.remove('hidden');
            document.getElementById('tab-' + aba).classList.add('border-blue-500', 'text-blue-600');
            document.getElementById('tab-' + aba).classList.remove('border-transparent', 'text-gray-600');
            
            if (aba === 'listar') carregarVeiculos();
            if (aba === 'relatorio') carregarRelatorio();
        }

        document.getElementById('form-entrada').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            formData.append('acao', 'registrar_entrada');
            
            try {
                const response = await fetch('index.php', { method: 'POST', body: formData });
                const data = await response.json();
                
                if (!data.sucesso) {
                    Swal.fire('Erro!', data.mensagem, 'error');
                    return;
                }
                
                Swal.fire('Sucesso!', data.mensagem, 'success');
                e.target.reset();
            } catch (error) {
                Swal.fire('Erro!', 'Erro ao processar requisição', 'error');
            }
        });

        document.getElementById('form-saida').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            formData.append('acao', 'registrar_saida');
            
            try {
                const response = await fetch('index.php', { method: 'POST', body: formData });
                const data = await response.json();
                
                if (!data.sucesso) {
                    Swal.fire('Erro!', data.mensagem, 'error');
                    return;
                }
                
                const getSvgIcon = (tipo) => {
                    if (tipo === 'carro') return '<svg class="w-20 h-20 mx-auto mb-4 text-blue-500" fill="currentColor" viewBox="0 0 64 64"><path d="M58,30h-4.5l-6-10c-0.8-1.3-2.2-2-3.7-2H20.2c-1.5,0-2.9,0.7-3.7,2l-6,10H6c-2.2,0-4,1.8-4,4v16c0,2.2,1.8,4,4,4h4v4c0,1.1,0.9,2,2,2h4c1.1,0,2-0.9,2-2v-4h28v4c0,1.1,0.9,2,2,2h4c1.1,0,2-0.9,2-2v-4h4c2.2,0,4-1.8,4-4V34C62,31.8,60.2,30,58,30z M18.8,22h26.4l4.5,8H14.3L18.8,22z M14,46c-2.2,0-4-1.8-4-4s1.8-4,4-4s4,1.8,4,4S16.2,46,14,46z M50,46c-2.2,0-4-1.8-4-4s1.8-4,4-4s4,1.8,4,4S52.2,46,50,46z"/><circle cx="14" cy="42" r="2" opacity="0.3"/><circle cx="50" cy="42" r="2" opacity="0.3"/></svg>';
                    if (tipo === 'moto') return '<svg class="w-20 h-20 mx-auto mb-4 text-purple-500" fill="currentColor" viewBox="0 0 64 64"><circle cx="14" cy="48" r="10" stroke="#fff" stroke-width="2"/><circle cx="50" cy="48" r="10" stroke="#fff" stroke-width="2"/><path d="M14,38c-5.5,0-10,4.5-10,10s4.5,10,10,10s10-4.5,10-10S19.5,38,14,38z M14,54c-3.3,0-6-2.7-6-6s2.7-6,6-6s6,2.7,6,6S17.3,54,14,54z"/><path d="M50,38c-5.5,0-10,4.5-10,10s4.5,10,10,10s10-4.5,10-10S55.5,38,50,38z M50,54c-3.3,0-6-2.7-6-6s2.7-6,6-6s6,2.7,6,6S53.3,54,50,54z"/><path d="M42,18h-8l-4,8h-6l-2-4h-6c-1.1,0-2,0.9-2,2v4c0,1.1,0.9,2,2,2h4l8,18h8l4-8l8,8h4V30l-8-12H42z"/><rect x="38" y="14" width="8" height="4" rx="2"/><circle cx="14" cy="48" r="3" opacity="0.3"/><circle cx="50" cy="48" r="3" opacity="0.3"/></svg>';
                    return '<svg class="w-20 h-20 mx-auto mb-4 text-orange-500" fill="currentColor" viewBox="0 0 64 64"><path d="M58,30h-6V20c0-2.2-1.8-4-4-4H6c-2.2,0-4,1.8-4,4v22c0,2.2,1.8,4,4,4h2c0,4.4,3.6,8,8,8s8-3.6,8-8h16c0,4.4,3.6,8,8,8s8-3.6,8-8h2c2.2,0,4-1.8,4-4V34C62,31.8,60.2,30,58,30z M16,50c-2.2,0-4-1.8-4-4s1.8-4,4-4s4,1.8,4,4S18.2,50,16,50z M48,50c-2.2,0-4-1.8-4-4s1.8-4,4-4s4,1.8,4,4S50.2,50,48,50z M52,38h-4V30h4l6,6v2H52z"/><rect x="8" y="20" width="6" height="8" rx="1" opacity="0.3"/><rect x="16" y="20" width="10" height="8" rx="1" opacity="0.3"/><rect x="28" y="20" width="6" height="8" rx="1" opacity="0.3"/><circle cx="16" cy="46" r="2" opacity="0.3"/><circle cx="48" cy="46" r="2" opacity="0.3"/></svg>';
                };
                
                Swal.fire({
                    title: 'Saída Registrada!',
                    html: `
                        <div class="text-center">
                            ${getSvgIcon(data.veiculo.tipo)}
                            <div class="bg-gray-50 rounded-lg p-4 space-y-2">
                                <p class="text-lg"><strong>Placa:</strong> <span class="font-mono">${data.veiculo.placa}</span></p>
                                <p class="text-lg"><strong>Tipo:</strong> ${data.veiculo.tipo.charAt(0).toUpperCase() + data.veiculo.tipo.slice(1)}</p>
                                <p class="text-lg"><strong>Tempo:</strong> ${data.horas}h estacionado</p>
                            </div>
                            <div class="bg-green-50 rounded-lg p-6 mt-4">
                                <p class="text-sm text-gray-600 mb-2">Valor a pagar:</p>
                                <p class="text-4xl font-bold text-green-600">R$ ${data.tarifa.toFixed(2)}</p>
                            </div>
                        </div>
                    `,
                    icon: 'success',
                    confirmButtonColor: '#10B981'
                });
                e.target.reset();
            } catch (error) {
                Swal.fire('Erro!', 'Erro ao processar requisição', 'error');
            }
        });

        async function carregarVeiculos() {
            try {
                const response = await fetch('index.php?acao=listar_veiculos');
                const data = await response.json();
                
                if (data.sucesso) {
                    const lista = document.getElementById('lista-veiculos');
                    
                    if (data.veiculos.length === 0) {
                        lista.innerHTML = '<p class="text-gray-500">Nenhum veículo estacionado no momento.</p>';
                        return;
                    }
                    
                    let html = '<table class="min-w-full divide-y divide-gray-200"><thead class="bg-gray-50"><tr>';
                    html += '<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Placa</th>';
                    html += '<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>';
                    html += '<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Entrada</th>';
                    html += '</tr></thead><tbody class="bg-white divide-y divide-gray-200">';
                    
                    data.veiculos.forEach(v => {
                        const svgIcon = v.tipo === 'carro' 
                            ? '<svg class="w-8 h-8 text-blue-500" fill="currentColor" viewBox="0 0 64 64"><path d="M58,30h-4.5l-6-10c-0.8-1.3-2.2-2-3.7-2H20.2c-1.5,0-2.9,0.7-3.7,2l-6,10H6c-2.2,0-4,1.8-4,4v16c0,2.2,1.8,4,4,4h4v4c0,1.1,0.9,2,2,2h4c1.1,0,2-0.9,2-2v-4h28v4c0,1.1,0.9,2,2,2h4c1.1,0,2-0.9,2-2v-4h4c2.2,0,4-1.8,4-4V34C62,31.8,60.2,30,58,30z M18.8,22h26.4l4.5,8H14.3L18.8,22z M14,46c-2.2,0-4-1.8-4-4s1.8-4,4-4s4,1.8,4,4S16.2,46,14,46z M50,46c-2.2,0-4-1.8-4-4s1.8-4,4-4s4,1.8,4,4S52.2,46,50,46z"/></svg>'
                            : v.tipo === 'moto'
                            ? '<svg class="w-8 h-8 text-purple-500" fill="currentColor" viewBox="0 0 64 64"><circle cx="14" cy="48" r="10" stroke="#fff" stroke-width="1"/><circle cx="50" cy="48" r="10" stroke="#fff" stroke-width="1"/><path d="M14,38c-5.5,0-10,4.5-10,10s4.5,10,10,10s10-4.5,10-10S19.5,38,14,38z M14,54c-3.3,0-6-2.7-6-6s2.7-6,6-6s6,2.7,6,6S17.3,54,14,54z"/><path d="M50,38c-5.5,0-10,4.5-10,10s4.5,10,10,10s10-4.5,10-10S55.5,38,50,38z M50,54c-3.3,0-6-2.7-6-6s2.7-6,6-6s6,2.7,6,6S53.3,54,50,54z"/><path d="M42,18h-8l-4,8h-6l-2-4h-6c-1.1,0-2,0.9-2,2v4c0,1.1,0.9,2,2,2h4l8,18h8l4-8l8,8h4V30l-8-12H42z"/></svg>'
                            : '<svg class="w-8 h-8 text-orange-500" fill="currentColor" viewBox="0 0 64 64"><path d="M58,30h-6V20c0-2.2-1.8-4-4-4H6c-2.2,0-4,1.8-4,4v22c0,2.2,1.8,4,4,4h2c0,4.4,3.6,8,8,8s8-3.6,8-8h16c0,4.4,3.6,8,8,8s8-3.6,8-8h2c2.2,0,4-1.8,4-4V34C62,31.8,60.2,30,58,30z M16,50c-2.2,0-4-1.8-4-4s1.8-4,4-4s4,1.8,4,4S18.2,50,16,50z M48,50c-2.2,0-4-1.8-4-4s1.8-4,4-4s4,1.8,4,4S50.2,50,48,50z M52,38h-4V30h4l6,6v2H52z"/></svg>';
                        
                        html += '<tr class="hover:bg-gray-50">';
                        html += `<td class="px-6 py-4 whitespace-nowrap"><div class="flex items-center space-x-3"><div>${svgIcon}</div><span class="font-medium font-mono">${v.placa}</span></div></td>`;
                        html += `<td class="px-6 py-4 whitespace-nowrap capitalize">${v.tipo}</td>`;
                        html += `<td class="px-6 py-4 whitespace-nowrap">${new Date(v.dataHoraEntrada).toLocaleString('pt-BR')}</td>`;
                        html += '</tr>';
                    });
                    
                    html += '</tbody></table>';
                    lista.innerHTML = html;
                }
            } catch (error) {
                console.error(error);
            }
        }

        async function carregarRelatorio() {
            try {
                const response = await fetch('index.php?acao=gerar_relatorio');
                const data = await response.json();
                
                const container = document.getElementById('conteudo-relatorio');
                
                let html = '<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">';
                html += `<div class="bg-blue-50 p-4 rounded-lg"><h3 class="font-semibold text-lg">Total de Veículos</h3><p class="text-3xl font-bold text-blue-600">${data.total_geral}</p></div>`;
                html += `<div class="bg-green-50 p-4 rounded-lg"><h3 class="font-semibold text-lg">Faturamento Total</h3><p class="text-3xl font-bold text-green-600">R$ ${data.faturamento_total.toFixed(2)}</p></div>`;
                html += '</div>';
                
                html += '<h3 class="text-xl font-semibold mb-4">Por Tipo de Veículo</h3>';
                html += '<div class="grid grid-cols-1 md:grid-cols-3 gap-4">';
                
                for (const [tipo, info] of Object.entries(data.por_tipo)) {
                    html += `<div class="border border-gray-200 p-4 rounded-lg">`;
                    html += `<h4 class="font-semibold text-lg capitalize mb-2">${info.tipo}</h4>`;
                    html += `<p><strong>Quantidade:</strong> ${info.quantidade}</p>`;
                    html += `<p><strong>Valor/hora:</strong> R$ ${info.valor_hora.toFixed(2)}</p>`;
                    html += `<p class="text-xl font-bold text-green-600 mt-2">R$ ${info.faturamento.toFixed(2)}</p>`;
                    html += `</div>`;
                }
                
                html += '</div>';
                container.innerHTML = html;
            } catch (error) {
                console.error(error);
            }
        }

        mostrarAba('entrada');
        
        document.getElementById('placa-entrada').addEventListener('input', function(e) {
            let value = e.target.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
            
            if (value.length > 3) {
                value = value.substring(0, 3) + '-' + value.substring(3, 7);
            }
            
            e.target.value = value;
        });
    </script>
</body>
</html>
