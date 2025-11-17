# Sistema de Controle de Estacionamento

## Integrantes
- Nome: Matheus Mikio
- RA: 2005154

## Descrição
Sistema de controle de estacionamento desenvolvido em PHP seguindo princípios de POO, padrões SOLID, Clean Code, Object Calisthenics, DRY e KISS. O sistema permite registro de entrada e saída de veículos, cálculo automático de tarifas baseado no tipo de veículo (carro, moto, caminhão), geração de relatórios e gerenciamento de veículos estacionados com validações aplicando boas práticas de design orientado a objetos através de interfaces, injeção de dependências e padrões de projeto como Strategy e Factory.

## Princípios de Design Aplicados

### SOLID
Este projeto implementa os princípios SOLID:

1. **Single Responsibility Principle (SRP)**
   - Cada classe tem uma responsabilidade única
   - `VeiculoRepository` responsável apenas pela persistência
   - `CalculadoraTarifa` focada apenas no cálculo de tarifas
   - `EstacionamentoService` coordena operações de estacionamento

2. **Open/Closed Principle (OCP)**
   - Estratégias de tarifa extensíveis através de `IEstrategiaTarifa`
   - Novos tipos de veículo podem ser adicionados sem modificar código existente
   - Factory Pattern permite criação de novos veículos sem alteração

3. **Liskov Substitution Principle (LSP)**
   - Subclasses `Carro`, `Moto`, `Caminhao` podem substituir `Veiculo` sem quebrar funcionalidade
   - Implementações de `IEstrategiaTarifa` intercambiáveis

4. **Interface Segregation Principle (ISP)**
   - Interfaces específicas: `ICalculadoraTarifa`, `IVeiculoRepository`, etc.
   - Clientes dependem apenas dos métodos que usam

5. **Dependency Inversion Principle (DIP)**
   - Dependências injetadas via construtor
   - Código depende de abstrações (interfaces), não implementações concretas

### Object Calisthenics
Este projeto implementa os seguintes conceitos de Object Calisthenics:

1. **Classes Responsáveis por Si Mesmas**
   - Cada classe valida seus próprios dados
   - `Veiculo` valida placa e datas na construção
   - Serviços coordenam operações sem validar dados primitivos

2. **Use Apenas Um Ponto Por Linha (Only One Dot Per Line)**
   - Lei de Deméter Aplicada: Comunicação apenas com vizinhos imediatos
   - Encapsulamento Respeitado: Evita chains de métodos
   - Responsabilidade Distribuída: Cada classe expõe apenas o necessário

3. **Imutabilidade através de Value Objects**
   - Placas e datas são validadas e normalizadas na criação
   - Mudanças requerem criação de novos objetos
   - Garante integridade dos dados

4. **Expressividade do Domínio**
   - Código auto-documentado através de nomes expressivos
   - Impossível criar objetos inválidos (fail-fast principle)
   - Regras de negócio próximas aos dados

### DRY (Don't Repeat Yourself)
Eliminação de Duplicação de Código:
- Validações Centralizadas: Regras de validação em classes específicas
- Reutilização de Estratégias: `TarifaCarro`, `TarifaMoto`, `TarifaCaminhao` reutilizam interface comum
- Métodos Únicos: Cada funcionalidade implementada uma vez
- Factory Patterns: `FabricaVeiculo` e `FabricaTarifa` evitam duplicação de lógica de criação

Benefícios Alcançados:
- Manutenção simplificada: mudanças em um local apenas
- Redução de bugs: consistência garantida
- Código mais limpo e legível

### KISS (Keep It Simple, Stupid)
Simplicidade na Arquitetura:
- Classes Pequenas e Focadas: Cada classe uma responsabilidade
- Métodos Simples: Funções pequenas e diretas
- Hierarquia Flat: Composição em vez de herança complexa

Simplicidade na Implementação:
- Sem Over-Engineering: Funcionalidades diretas
- Dependências Mínimas: Apenas PHP + SQLite
- Estrutura Clara: Organização lógica de arquivos
- Testes Diretos: Lógica simples de testar

Benefícios Alcançados:
- Código fácil de entender
- Debugging simplificado
- Menor curva de aprendizado
- Redução de complexidade desnecessária

## Como Executar
1. Instale o XAMPP e inicie Apache + MySQL (SQLite será criado automaticamente).
2. Coloque a pasta `controle_estacionamento` dentro de `htdocs`.
3. Acesse: `http://localhost/controle_estacionamento/public/`

## Funcionalidades Implementadas
**Registro de Entrada de Veículos**
- Validação automática de placa através da classe `Veiculo`
- Verificação de veículo já estacionado
- Criação automática via `FabricaVeiculo`
- Persistência através de `VeiculoRepository`

**Registro de Saída de Veículos**
- Cálculo automático de tarifa via `CalculadoraTarifa`
- Atualização de status do veículo
- Validação de veículo existente e ativo

**Listagem de Veículos Estacionados**
- Visualização de todos os veículos ativos
- Dados formatados com placa, tipo, data/hora entrada

**Geração de Relatórios**
- Relatório completo com veículos estacionados
- Cálculo de tarifas para cada veículo
- Totais por tipo de veículo

## Regras de Negócio

### Validação de Placa
- Formato brasileiro padrão (XXX-XXXX)
- Apenas letras e números permitidos
- Conversão automática para maiúsculo
- Não pode estar vazia

### Validação de Tipo de Veículo
- Tipos aceitos: carro, moto, caminhão
- Case-insensitive
- Validação na criação via Factory

### Cálculo de Tarifas
- **Carro**: R$ 5,00 por hora
- **Moto**: R$ 3,00 por hora  
- **Caminhão**: R$ 10,00 por hora
- Cálculo baseado em horas estacionadas
- Arredondamento para cima a partir de 1 minuto

### Controle de Estacionamento
- Um veículo não pode estar estacionado duas vezes simultaneamente
- Saída só permitida para veículos ativos
- Histórico mantido no banco de dados

## Casos de Uso Documentados

1. **Registro de Entrada Válido**
   - Cenário: Veículo válido entra no estacionamento
   - Entrada: Placa: "ABC-1234", Tipo: "carro"
   - Resultado: Criação bem-sucedida e persistência

2. **Placa Inválida**
   - Cenário: Tentativa com placa em formato incorreto
   - Entrada: Placa: "ABC123", Tipo: "carro"
   - Resultado: Exception lançada pela validação de placa

3. **Veículo Já Estacionado**
   - Cenário: Mesmo veículo tenta entrar novamente
   - Entrada: Placa: "ABC-1234", Tipo: "carro"
   - Resultado: Erro detectado pelo serviço

4. **Registro de Saída**
   - Cenário: Veículo sai após período estacionado
   - Entrada: Placa: "ABC-1234"
   - Resultado: Cálculo de tarifa e atualização de status

5. **Saída de Veículo Inexistente**
   - Cenário: Tentativa de saída sem entrada prévia
   - Entrada: Placa: "XYZ-9999"
   - Resultado: Erro de veículo não encontrado

6. **Listagem de Veículos**
   - Cenário: Visualização de veículos estacionados
   - Entrada: Chamada do método listarVeículosEstacionados()
   - Resultado: Lista formatada com dados dos veículos
m
7. **Geração de Relatório**
   - Cenário: Relatório completo do estacionamento
   - Entrada: Chamada do método gerarRelatorio()
   - Resultado: Dados agregados com totais por tipo

## Arquitetura e Design Patterns

### Padrões Implementados

**Strategy Pattern**
- `IEstrategiaTarifa` e implementações `TarifaCarro`, `TarifaMoto`, `TarifaCaminhao`
- Permite cálculo de tarifa extensível por tipo de veículo
- Fácil adição de novos tipos sem modificar código existente

**Factory Pattern**
- `FabricaVeiculo`: Cria instâncias de `Veiculo` baseadas no tipo
- `FabricaTarifa`: Cria estratégias de tarifa apropriadas
- Centraliza lógica de criação e validação

**Repository Pattern**
- `VeiculoRepository`: Abstrai acesso aos dados
- Interface `IVeiculoRepository` permite diferentes implementações
- Separa lógica de negócio da persistência

**Service Layer Pattern**
- `EstacionamentoService`: Coordena operações complexas
- `RelatorioService`: Lógica específica de relatórios
- Mantém controllers enxutos

### Vantagens da Abordagem
- **SOLID + Clean Code**: Código modular, testável e manutenível
- **Object Calisthenics + DRY**: Eliminação de duplicação com encapsulamento
- **Strategy + Factory**: Extensibilidade sem modificação
- **Repository + Service**: Separação clara de responsabilidades

### Benefícios Específicos
- **Redução de Acoplamento**: Interfaces e injeção de dependências
- **Facilita Testes**: Classes isoladas e mockáveis
- **Manutenibilidade**: Mudanças localizadas
- **Reutilização**: Componentes intercambiáveis
- **Segurança de Tipos**: Validações em tempo de criação
- **Imutabilidade**: Dados consistentes e thread-safe