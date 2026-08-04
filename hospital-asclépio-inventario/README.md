# 🏥 Hospitais Asclépio — Sistema de Inventário TI

![Versão](https://img.shields.io/badge/vers%C3%A3o-v1.0.2-emerald?style=for-the-badge)
![PHP](https://img.shields.io/badge/PHP-8.5-blue?style=for-the-badge&logo=php)
![Laravel](https://img.shields.io/badge/Laravel-v13.17-red?style=for-the-badge&logo=laravel)
![Livewire](https://img.shields.io/badge/Livewire-v4.1-pink?style=for-the-badge&logo=livewire)
![Flux UI](https://img.shields.io/badge/Flux_UI-v2.15-06B6D4?style=for-the-badge)
![TailwindCSS](https://img.shields.io/badge/TailwindCSS-v4.0-06B6D4?style=for-the-badge&logo=tailwindcss)
![Pest](https://img.shields.io/badge/Pest-v4.7-purple?style=for-the-badge&logo=pest)

O **Sistema de Inventário Hospitais Asclépio** é uma solução web reativa corporativa desenvolvida para digitalizar, padronizar e gerenciar o parque tecnológico (desktops, notebooks, monitores e periféricos) da equipe de Tecnologia da Informação no ambiente hospitalar.

A aplicação possui um **modelo dinâmico de auto-retroalimentação de dados**, onde o uso cotidiano do sistema pelos Coordenadores e Administradores expande e vincula automaticamente a estrutura orgânica (Usuários $\rightarrow$ Locais $\rightarrow$ Setores $\rightarrow$ Secretarias/Unidades $\rightarrow$ Equipamentos). Tudo isso com auditoria rigorosa de autoria de cada alteração, lixeira controlada com níveis de exclusão (arquivamento vs. expurgo definitivo), geração de relatórios oficiais em formato PDF e dashboard gerencial em tempo real, em conformidade com as diretrizes da **Lei Geral de Proteção de Dados (LGPD)**.

---

## 🔄 Arquitetura de Auto-Retroalimentação de Dados

O grande diferencial do sistema é sua capacidade de **auto-retroalimentar a base de dados** dinamicamente com base nas operações do dia a dia, eliminando cadastros redundantes e prevenindo inconsistências de dados:

```
[Coordenador do Setor/Local] ──cria usuario──> [Novo Usuário herda Setor + Local + Unidade/Secretaria]
                                                               │
[Administrador / TI] ──cadastra PC/Note──> [Equipamento vinculado ao Setor + Local + Unidade/Secretaria]
                                                               │
                                         ▼
                 [Expansão e Vinculação Orgânica da Rede de Dados]
```

1. **Retroalimentação de Locais e Unidades via Coordenadores**:
   - Ao cadastrar um novo usuário, o **Coordenador** não precisa escolher ou reconfigurar o local. O sistema vincula automaticamente a conta ao **Setor e Local** do próprio Coordenador.
   - Esse vínculo fortalece em tempo real a associação do Local à sua **Unidade/Secretaria** correspondente no banco de dados.

2. **Retroalimentação do Parque Tecnológico via Administradores**:
   - Sempre que a equipe de TI (**Administrador**) cadastra um novo computador (`desktop` ou `notebook`), monitor ou periférico, o ativo é amarrado diretamente ao `Setor` e ao `Local` físico.
   - Essa ação mapeia e atualiza continuamente a distribuição geográfica de hardware por unidade hospitalar.

3. **Ciclo de Arquivamento e Expurgo**:
   - As exclusões efetuadas por Administradores passam por **Soft Delete** (arquivamento), mantendo o histórico de auditoria (`spatie/laravel-activitylog`).
   - O **Diretor** tem a visão macro completa para gerenciar a lixeira, podendo restaurar itens arquivados ou realizar o expurgo definitivo (*Hard Delete*).

---

## 🛠️ Modos de Acesso e Permissões (ACL)

O sistema possui **4 níveis de acesso hierárquicos e especializados**:

| Nível de Acesso | Perfil (`role`) | Visão no Sistema | Permissões & Capacidades Principais |
| :--- | :---: | :--- | :--- |
| 👑 **Diretor** | `diretor` | **Macro Total** | • Visão irrestrita de todos os setores, locais, unidades e usuários.<br>• **Exclusão Definitiva (Hard Delete)** e expurgo de lixeira.<br>• Gerenciamento completo de contas (pode excluir contas de verdade).<br>• Restauração de registros e emissão de relatórios consolidados. |
| 🛡️ **Administrador** | `administrador` | **TI & Infraestrutura** | • Acesso amplo operacional da equipe de TI / Desenvolvimento.<br>• Cadastro e atualização de computadores, monitores, periféricos e setores.<br>• **Exclusão via Arquivamento (Soft Delete)**, enviando itens para a lixeira.<br>• Criação de contas de outros Administradores, Coordenadores e Usuários.<br>• Registro de equipamentos vinculando produto ao Setor e Local físico. |
| 👔 **Coordenador** | `coordenador` | **Gestão Setorial** | • Visão delimitada ao seu setor/local de atuação.<br>• **Criação de Usuários**: Pode cadastrar contas para sua equipe.<br>• **Vinculação Automática**: Novos usuários criados recebem automaticamente o seu Local/Setor.<br>• Retroalimentação das conexões entre Locais e Unidades. |
| 👤 **Usuário** | `usuario` | **Operacional / Consulta** | • Acesso restrito ao escopo do seu próprio Local/Setor.<br>• Visualização e acompanhamento dos equipamentos alocados na sua unidade. |

---

## 🛠️ Funcionalidades Detalhadas

1. ### **Gerenciamento Dinâmico de Setores (`SetorManager`)**
   - Inclusão dinâmica de secretarias/unidades, departamentos ou minisetores hospitalares sem dependência de cadastros fixos.
   - Modelo Eloquent `Setor` atrelado ao autor da criação via campo `created_by` e suporte a Soft Deletes (`deleted_at`).
   - Validação de nomes únicos para evitar duplicidade de cadastros organizacionais.

2. ### **Controle e Cadastro de Equipamentos (`EquipamentoForm`)**
   - Formatação reativa para inclusão de computadores com enum de tipo `tipo` (`desktop` ou `notebook`).
   - Campo exclusivo de **Desempenho** (`tipo_desempenho`), categorizando o equipamento como `basico`, `intermediario` ou `avancado`.
   - Registro de campos obrigatórios e opcionais: `serial` (número de série do fabricante), `marca_modelo` (ex: "Dell OptiPlex 3080", "Lenovo ThinkPad L14"), `kit_teclado_mouse_locado` (flag booleana para controle de contratos de locação) e `responsavel_levantamento` (nome do técnico responsável pela coleta de campo).
   - Associação direta ao setor correspondente via `setor_id`.

3. ### **Gerenciamento Reativo de Múltiplos Monitores (`Monitor`)**
   - Relação **1:N** entre `Equipamento` e `Monitor`.
   - Gerenciamento dinâmico em tela através do estado de arrays no Livewire, permitindo adicionar ou remover novos monitores vinculados a um computador antes do salvamento final.
   - Registro sequencial `numero` (1, 2, 3...) e número de série individual `serial` para cada tela alocada.

4. ### **Mapeamento de Periféricos Avulsos ou Vinculados (`PerifericoManager`)**
   - Cadastro de equipamentos complementares (como impressoras, leitores de código de barras, no-breaks, scanners e switches).
   - Suporta vinculação direta a um setor (`setor_id`) e opcionalmente a um computador específico (`equipamento_id` nulo ou preenchido).
   - Campos de identificação: `tipo`, `serial_patrimonio` e bloco textual `observacoes`.

5. ### **Trilha de Auditoria e Histórico Transparente (`HistoricoModal`)**
   - Integração com o pacote `spatie/laravel-activitylog` nas entidades `Equipamento`, `Setor`, `Monitor` e `Periferico`.
   - Registro automático das alterações contendo o estado anterior (`before`), novo estado (`after`), ID do usuário responsável (`causer_id`) e data/hora.
   - Modal reativo `HistoricoModal` acessível em cada item com exibição em linha do tempo legível.

6. ### **Exclusão Segura com Soft Delete e Lixeira de Expurgo (`LixeiraManager`)**
   - Exclusões efetuadas por Administradores acionam o trait `SoftDeletes` sem perda imediata dos dados.
   - Painel `LixeiraManager` exclusivo para os perfis autorizados (com gestão avançada pelo Diretor), permitindo restauração em lote ou unitária dos registros.
   - Exclusão definitiva (*Force Delete*) efetuada pelo Diretor protegida por modal com exigência da digitação exata da palavra-chave de segurança `"CONFIRMAR"`.

7. ### **Busca Reativa Inteligente e Exportação PDF (`BuscaSetor` e `PdfController`)**
   - Filtro global em tempo real via `wire:model.live` para localização instantânea por serial, modelo, responsável ou nome do setor.
   - Exportação de relatórios consolidados em formato PDF otimizado para impressão oficial utilizando a biblioteca `barryvdh/laravel-dompdf` na rota `/relatorios/setor/{setor}/pdf`.

8. ### **Dashboard Gerencial em Tempo Real (`Dashboard`)**
   - Cards com indicadores consolidados (total de computadores, monitores, periféricos e setores ativos).
   - Gráficos estatísticos visuais: proporção entre desktops e notebooks, nivelamento de desempenho (`básico`, `intermediário`, `avançado`) e ranking Top 10 dos setores com maior volume de equipamentos.
   - Feed de auditoria exibindo as atividades mais recentes gravadas no sistema.

9. ### **Painel Exclusivo de Gestão de Usuários (`UserManagement`)**
   - Interface padronizada com **Flux UI** para criação e edição de novos usuários.
   - Suporte à alteração de senhas com hashing seguro Bcrypt.
   - Atribuição controlada de perfis (`diretor`, `administrador`, `coordenador`, `usuario`) conforme a hierarquia do operador logado.

---

## 💻 Stack Tecnológica & Versões

### Core do Backend

| Pacote | Versão Requerida | Finalidade |
| :--- | :---: | :--- |
| **PHP** | `^8.3` (Executado em `8.5`) | Linguagem base do sistema com construtores promovidos e tipagem estrita |
| **laravel/framework** | `^13.17` | Framework PHP principal (Kernel, ORM Eloquent, Auth, Rotas e Segurança) |
| **livewire/livewire** | `^4.1` | Motor de reatividade full-stack no servidor sem necessidade de SPA |
| **livewire/flux** | `^2.15` | Kit de componentes e ícones nativos UI padronizados para Livewire |
| **livewire/blaze** | `^1.0` | Otimizador de renderização e cache de componentes Blade do Livewire |
| **spatie/laravel-activitylog** | `^5.0` | Auditoria automática de mutations em models para conformidade e rastreabilidade |
| **barryvdh/laravel-dompdf** | `^3.1` | Gerador e renderizador de relatórios em formato PDF para impressão |
| **laravel/tinker** | `^3.0` | Console REPL para interações em tempo real com o ambiente da aplicação |

### Suite de Testes & Qualidade

| Pacote | Versão Requerida | Finalidade |
| :--- | :---: | :--- |
| **pestphp/pest** | `^4.7` | Framework de testes expressivo e moderno baseado em PHP |
| **pestphp/pest-plugin-laravel** | `^4.1` | Plugin de atalhos e integrações de testes nativas do Laravel para Pest |
| **laravel/pint** | `^1.27` | Formatador de código PHP baseado em padrões rigorosos de estilo |
| **larastan/larastan** | `^3.9` | Análise estática de código com validação de tipos PHPStan para Laravel |

### Core do Frontend

| Pacote / Ferramenta | Versão Requerida | Finalidade |
| :--- | :---: | :--- |
| **livewire/flux** | `^2.15` | Padronização de componentes visuais e biblioteca de ícones SVG nativos no Blade |
| **tailwindcss** | `^4.0.7` | Framework CSS utilitário com Design System institucional |
| **@tailwindcss/vite** | `^4.1.11` | Plugin oficial de integração do Tailwind CSS v4 com a pipeline Vite |
| **vite** | `^8.0.0` | Bundler e servidor de desenvolvimento ultra-rápido para assets front-end |

---

## ⚡ Tecnologia Principal: Livewire 4 & Blaze

A arquitetura do sistema adotou **Livewire 4** com a extensão **Livewire Blaze** como núcleo de interface do usuário pelas seguintes justificativas técnicas:

- **Eliminação de Camada API REST/GraphQL Extra**: A lógica de apresentação e regras de negócio permanecem totalmente no backend PHP, eliminando a complexidade de serialização de DTOs e controllers de API separados.
- **Estado Reativo no Servidor**: O estado da interface (como filtros de busca, modais e adição dinâmica de monitores) é gerenciado via propriedades da classe PHP do componente com sincronização em tempo real via diretiva `wire:model.live`.
- **Performance de Renderização com Blaze**: O uso do `livewire/blaze` otimiza a compilação e re-renderização das views Blade reativas, reduzindo o consumo de recursos no servidor.
- **Segurança Nativa e Autorização Integrada**: Operações reativas utilizam diretamente os Middlewares, Gates e Policies do Laravel (`$this->authorize()`), prevenindo modificações indevidas.

### Comandos Úteis do Livewire & Ferramentas do Projeto

```bash
# Limpar o cache de compilação de views otimizadas pelo Blaze
php artisan blaze:clear

# Executar a análise estática do Larastan em busca de erros de tipo
php artisan types:check

# Formatar o código PHP do projeto seguindo os padrões do Pint
vendor/bin/pint --dirty --format agent

# Iniciar o ambiente de desenvolvimento completo (servidor web, worker de fila e Vite)
composer run dev
```

---

## 🎨 Padronização de Componentes Front-End com Flux UI

A interface do usuário utiliza o **Flux UI** (`livewire/flux`) para padronização visual dos componentes e gerenciamento de ícones SVG nativos no Blade:

- **Componentes de Ícones Nativos**: Uso de `<flux:icon name="..." />` para ícones do sistema (como `<flux:icon name="user-plus" />`, `<flux:icon name="computer-desktop" />`, etc.).
- **Diretivas de Layout**: Inclusão de `@fluxAppearance` no `<head>` e `@fluxScripts` ao final do `<body>` nos layouts da aplicação (`app.blade.php` e `guest.blade.php`).
- **Suporte ao Tailwind CSS v4**: Mapeamento dos stubs do Flux em `resources/css/app.css` via `@source '../../vendor/livewire/flux/stubs/**/*.blade.php';`.

---

## ⚙️ Instalação e Configuração

### 📋 Pré-requisitos

- **PHP**: `^8.3` ou `8.5` (extensões habilitadas: `pdo_sqlite`, `mbstring`, `openssl`, `gd`, `xml`, `curl`, `zip`).
- **Composer**: `^2.0` ou superior.
- **Node.js**: `^18.0` ou `^20.0` e **NPM** `^10.0`.
- **Git**.

---

### ⚡ Setup Automático

```bash
git clone https://github.com/1Dereck/hospital-aetheris-inventario.git
cd hospital-aetheris-inventario
composer run setup
php artisan db:seed
composer run dev
```

---

### 🛠️ Setup Manual no Terminal

1. **Clonar o Repositório**:
   ```bash
   git clone https://github.com/1Dereck/hospital-aetheris-inventario.git
   cd hospital-aetheris-inventario
   ```

2. **Instalar Dependências**:
   ```bash
   composer install
   npm install
   ```

3. **Configurar Ambiente**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Executar Migrações e Seeders**:

   ```bash
   php artisan migrate --seed
   ```

5. **Compilar Assets e Iniciar**:
   ```bash
   npm run build
   composer run dev
   ```
   > A aplicação estará disponível em: **`http://localhost:8000`**.

---

## 🔑 Credenciais Padrão

Após executar os seeders (`php artisan migrate --seed`), os seguintes usuários pré-configurados estarão disponíveis:

| Perfil | Usuário (`username`) | Senha | Descrição |
| :--- | :---: | :---: | :--- |
| 👑 **Diretor** | `Diretor` | `DTi@123` | Visão macro irrestrita, exclusão definitiva (Hard Delete), expurgo da lixeira e gestão completa de usuários. |
| 🛡️ **Administrador / TI** | `Administrador` | `DTi@123` | Operador técnico de campo. Cadastro/atualização de hardware, setores e usuários, exclusão via arquivamento (Soft Delete). |
| 👔 **Coordenador** | `Coordenador` | `DTi@123` | Visão e gestão dos equipamentos e dados pertencentes à sua unidade/setores. |
| 👤 **Usuário** | `Usuário` | `DTi@123` | Visão e cadastros restritos estritamente ao seu setor alocado. |

---

## 🧪 Suíte de Testes (Pest 4)

```bash
# Executar toda a suíte de testes com formato compacto
php artisan test --compact

# Executar suíte completa com linter e verificação de tipos
composer test

# Filtrar por teste específico
php artisan test --compact --filter=EquipamentoFormTest
php artisan test --compact --filter=UserManagementTest
```

---

## 🔒 Segurança & Conformidade (LGPD)

- **Criptografia de Senhas**: Hash seguro via **Bcrypt** (`'password' => 'hashed'`).
- **Controle de Acesso (RBAC & Policies)**: Proteção rigorosa por papéis (`diretor`, `administrador`, `coordenador`, `usuario`) via Policies (`UserPolicy`, `EquipamentoPolicy`, `SetorPolicy`, etc.).
- **Trilha de Auditoria (LGPD)**: Rastreamento completo de inserções, alterações e exclusões com autor e timestamp via `Spatie Activitylog`.
- **Soft Delete & Lixeira Protegida**: Exclusão temporária por padrão. Expurgo definitivo protegido por confirmação por palavra-chave.
- **Proteções Web Nativas**: CSRF Token, sanitização Eloquent contra SQL Injection e escaping Blade contra XSS.
