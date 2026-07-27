# 📦 Sistema de Inventário TI

![Versão](https://img.shields.io/badge/vers%C3%A3o-v1.0.0-emerald?style=for-the-badge)
![PHP](https://img.shields.io/badge/PHP-8.5-blue?style=for-the-badge&logo=php)
![Laravel](https://img.shields.io/badge/Laravel-v13.17-red?style=for-the-badge&logo=laravel)
![Livewire](https://img.shields.io/badge/Livewire-v4.1-pink?style=for-the-badge&logo=livewire)
![TailwindCSS](https://img.shields.io/badge/TailwindCSS-v4.0-06B6D4?style=for-the-badge&logo=tailwindcss)
![Pest](https://img.shields.io/badge/Pest-v4.7-purple?style=for-the-badge&logo=pest)

O **Sistema de Inventário TI** é uma solução web reativa corporativa desenvolvida para digitalizar, padronizar e substituir o controle em papel do levantamento de parque tecnológico (desktops, notebooks, monitores e periféricos) da equipe de Tecnologia da Informação. O repositório contém uma aplicação full-stack Laravel reativa que gerencia a alocação de equipamentos por secretaria ou minisetor, mantendo um histórico detalhado de autoria de cada alteração para auditoria, lixeira controlada por nível de acesso, geração de relatórios oficiais em formato PDF e um dashboard gerencial com estatísticas em tempo real. A aplicação está em conformidade com as diretrizes de governança de TI e boas práticas da **Lei Geral de Proteção de Dados (LGPD)** no que tange ao rastreamento e auditoria rigorosa de manipulação de dados operacionais.

---

## 🛠️ Funcionalidades

1. ### **Controle de Acesso Diferenciado por Perfis (ACL)**
   - **Perfil Diretor (`diretor`)**: Autonomia total no sistema, permissão exclusiva para realizar exclusão suavizada (*Soft Delete*), acessar a lixeira (`LixeiraManager`), restaurar itens ou realizar a exclusão definitiva no banco de dados. Gerencia contas e permissões de usuários através do componente `UserManagement`.
   - **Perfil Administrador / TI (`administrador`)**: Perfil operacional da equipe de campo. Permite cadastrar e atualizar setores, computadores, monitores e periféricos, além de visualizar históricos e emitir relatórios PDF. Não possui permissão para excluir registros, acessar a lixeira ou gerenciar outros usuários.

2. ### **Gerenciamento Dinâmico de Setores (`SetorManager`)**
   - Inclusão dinâmica de secretarias, departamentos ou minisetores sem dependência de cadastros fixos.
   - Modelo Eloquent `Setor` atrelado ao autor da criação via campo `created_by` e suporte a Soft Deletes (`deleted_at`).
   - Validação de nomes únicos para evitar duplicidade de cadastros organizacionais.

3. ### **Controle e Cadastro de Equipamentos (`EquipamentoForm`)**
   - Formatação reativa para inclusão de computadores com enum de tipo `tipo` (`desktop` ou `notebook`).
   - Registro de campos obrigatórios e opcionais: `serial` (número de série do fabricante), `marca_modelo` (ex: "Dell OptiPlex 3080", "Lenovo ThinkPad L14"), `kit_teclado_mouse_locado` (flag booleana para controle de contratos de locação) e `responsavel_levantamento` (nome do técnico responsável pela coleta de campo).
   - Associação direta ao setor correspondente via `setor_id`.

4. ### **Gerenciamento Reativo de Múltiplos Monitores (`Monitor`)**
   - Relação **1:N** entre `Equipamento` e `Monitor`.
   - Gerenciamento dinâmico em tela através do estado de arrays no Livewire, permitindo adicionar ou remover novos monitores vinculados a um computador antes do salvamento final.
   - Registro sequencial `numero` (1, 2, 3...) e número de série individual `serial` para cada tela alocada.

5. ### **Mapeamento de Periféricos Avulsos ou Vinculados (`PerifericoManager`)**
   - Cadastro de equipamentos complementares (como impressoras, leitores de código de barras, no-breaks, scanners e switches).
   - Suporta vinculação direta a um setor (`setor_id`) e opcionalmente a um computador específico (`equipamento_id` nulo ou preenchido).
   - Campos de identificação: `tipo`, `serial_patrimonio` e bloco textual `observacoes`.

6. ### **Trilha de Auditoria e Histórico Transparente (`HistoricoModal`)**
   - Integração com o pacote `spatie/laravel-activitylog` nas entidades `Equipamento`, `Setor`, `Monitor` e `Periferico`.
   - Registro automático das alterações contendo o estado anterior (`before`), novo estado (`after`), ID do usuário responsável (`causer_id`) e data/hora.
   - Modal reativo `HistoricoModal` acessível em cada item com exibição em linha do tempo legível.

7. ### **Exclusão Segura com Soft Delete e Lixeira de Expurgo (`LixeiraManager`)**
   - Exclusões efetuadas pelo Diretor acionam o trait `SoftDeletes` sem perda imediata dos dados.
   - Painel `LixeiraManager` exclusivo para o perfil `diretor` permitindo a restauração em lote ou unitária dos registros.
   - Exclusão definitiva (*Force Delete*) protegida por modal com exigência da digitação exata da palavra-chave de segurança `"CONFIRMAR"`.

8. ### **Busca Reativa Inteligente e Exportação PDF (`BuscaSetor` e `PdfController`)**
   - Filtro global em tempo real via `wire:model.live` para localização instantânea por serial, modelo, responsável ou nome do setor.
   - Exportação de relatórios consolidados em formato PDF otimizado para impressão oficial utilizando a biblioteca `barryvdh/laravel-dompdf` na rota `/relatorios/setor/{setor}/pdf`.

9. ### **Dashboard Gerencial em Tempo Real (`Dashboard`)**
   - Cards com indicadores consolidados (total de computadores, monitores, periféricos e setores ativos).
   - Gráficos estatísticos visuais: proporção entre desktops e notebooks e ranking Top 10 dos setores com maior volume de equipamentos.
   - Feed de auditoria exibindo as 10 atividades mais recentes gravadas no sistema.
   - Alerta visual direcionado ao perfil Diretor indicando o número de itens pendentes na lixeira.

10. ### **Painel Exclusivo de Gestão de Usuários (`UserManagement`)**
    - Interface restrita ao Diretor para criação de novos operadores de TI, alteração de senhas com algoritmo de hash seguro e atribuição dos perfis `diretor` ou `administrador`.
    - Inativação segura de contas de usuários através de *Soft Delete*.

---

## 💻 Stack Tecnológica & Versões

### Core do Backend

| Pacote | Versão Requerida | Finalidade |
| :--- | :---: | :--- |
| **PHP** | `^8.3` (Executado em `8.5`) | Linguagem base do sistema com construtores promovidos e tipagem estrita |
| **laravel/framework** | `^13.17` | Framework PHP principal (Kernel, ORM Eloquent, Auth, Rotas e Segurança) |
| **livewire/livewire** | `^4.1` | Motor de reatividade full-stack no servidor sem necessidade de SPA |
| **livewire/blaze** | `^1.0` | Otimizador de renderização e cache de componentes Blade do Livewire |
| **spatie/laravel-activitylog** | `^5.0` | Auditoria automática de mutations em models para conformidade e rastreabilidade |
| **barryvdh/laravel-dompdf** | `^3.1` | Gerador e renderizador de relatórios em formato PDF para impressão |
| **laravel/tinker** | `^3.0` | Console REPL para interações em tempo real com o ambiente da aplicação |

### Ecossistema de Desenvolvimento & Linter

| Pacote | Versão Requerida | Finalidade |
| :--- | :---: | :--- |
| **laravel/pint** | `^1.27` | Formatador de código PHP baseado em padrões rigorosos de estilo |
| **larastan/larastan** | `^3.9` | Análise estática de código com validação de tipos PHPStan para Laravel |
| **laravel/boost** | `^2.2` | Ferramentas de otimização e auxílio de desenvolvimento mantidos pela Laravel |
| **laravel/pail** | `^1.2.5` | Leitor e gerenciador de logs em tempo real via linha de comando |
| **laravel-lang/publisher** | `^16.8` | Gerenciador de traduções e arquivos de localização de mensagens do sistema |
| **barryvdh/laravel-ide-helper** | `^3.7` | Gerador de autocompletion e anotações PHPDoc para IDEs |
| **laravel/sail** | `^1.53` | Ambiente de desenvolvimento containerizado via Docker |

### Suite de Testes

| Pacote | Versão Requerida | Finalidade |
| :--- | :---: | :--- |
| **pestphp/pest** | `^4.7` | Framework de testes expressivo e moderno baseado em PHP |
| **pestphp/pest-plugin-laravel** | `^4.1` | Plugin de atalhos e integrações de testes nativas do Laravel para Pest |
| **fakerphp/faker** | `^1.24` | Gerador de dados fictícios para testes unitários e seeders de banco |
| **mockery/mockery** | `^1.6` | Framework de objetos simulação (mocks) para testes isolados |
| **nunomaduro/collision** | `^8.9.3` | Manipulador de erros e exibição detalhada de exceções no terminal |

### Core do Frontend

| Pacote / Ferramenta | Versão Requerida | Finalidade |
| :--- | :---: | :--- |
| **tailwindcss** | `^4.0.7` | Framework CSS utilitário com Design System institucional em Slate/Emerald |
| **@tailwindcss/vite** | `^4.1.11` | Plugin oficial de integração do Tailwind CSS v4 com a pipeline Vite |
| **vite** | `^8.0.0` | Bundler e servidor de desenvolvimento ultra-rápido para assets front-end |
| **laravel-vite-plugin** | `^3.1` | Integração do Vite com o sistema de views Blade e rotas do Laravel |
| **concurrently** | `^9.0.1` | Execução em paralelo dos processos de servidor PHP, fila e Vite em terminal único |

---

## ⚡ Tecnologia Principal: Livewire 4 & Blaze

A arquitetura do sistema adotou **Livewire 4** com a extensão **Livewire Blaze** como núcleo de interface do usuário pelas seguintes justificativas técnicas:

- **Eliminação de Camada API REST/GraphQL Extra**: A lógica de apresentação e regras de negócio permanecem totalmente no backend PHP, eliminando a complexidade de serialização de DTOs, controllers de API separados e clientes HTTP no front-end.
- **Estado Reativo no Servidor**: O estado da interface (como filtros de busca, modais e adição dinâmica de monitores) é gerenciado via propriedades da classe PHP do componente com sincronização em tempo real via diretiva `wire:model.live`.
- **Performance de Renderização com Blaze**: O uso do `livewire/blaze` otimiza a compilação e re-renderização das views Blade reativas, reduzindo drasticamente o consumo de CPU e memória no servidor e o tamanho dos payloads enviados via JSON.
- **Segurança Nativa e Autorização Integrada**: Operações reativas utilizam diretamente os Middlewares, Gates e Policies do Laravel (`$this->authorize()`), prevenindo modificações indevidas no DOM via DevTools do navegador.

### Comandos Úteis do Livewire & Ferramentas do Projeto

```bash
# Criar um novo componente Livewire (classe + view Blade)
php artisan make:livewire NomeDoComponente

# Limpar o cache de compilação de views otimizadas pelo Blaze
php artisan blaze:clear

# Executar a análise estática do Larastan em busca de erros de tipo
php artisan types:check # ou npx/vendor: phpstan analyse

# Formatar o código PHP do projeto seguindo os padrões do Pint
vendor/bin/pint --dirty --format agent

# Iniciar o ambiente de desenvolvimento completo (servidor web, worker de fila e Vite)
composer run dev
```

---

## ⚙️ Instalação e Configuração

Siga os passos abaixo para configurar o ambiente de desenvolvimento local:

### 1. Clonar o Repositório
```bash
# Clone o repositório para sua máquina local
git clone https://github.com/1Dereck/controle-equipamentos.git
cd controle-equipamentos
```

### 2. Instalar Dependências do PHP e Node.js
```bash
# Instala as dependências gerenciadas pelo Composer
composer install

# Instala os pacotes de front-end gerenciados pelo NPM
npm install
```

### 3. Configurar Variáveis de Ambiente
```bash
# Cria o arquivo .env a partir do modelo de exemplo
cp .env.example .env
```
> Ajuste as variáveis de conexão com o banco de dados no `.env` (`DB_CONNECTION`, `DB_DATABASE`, etc.). Por padrão, o projeto utiliza `sqlite`.

### 4. Gerar Chave da Aplicação
```bash
# Gera a chave de criptografia do Laravel
php artisan key:generate
```

### 5. Executar Migrações e Poblagem de Dados (Seeders)
```bash
# Executa as migrations do banco de dados e insere a massa de dados inicial
php artisan migrate --seed
```

### 6. Compilar Assets do Frontend
```bash
# Compila os arquivos CSS/JS para produção
npm run build
```

### 7. Iniciar o Servidor de Desenvolvimento
```bash
# Inicia a aplicação web, worker de filas e servidor Vite simultaneamente
composer run dev
```
> A aplicação estará acessível no navegador em: `http://localhost:8000`.

---

## 🔑 Credenciais Padrão

Após executar os seeders (`php artisan migrate --seed`), os seguintes usuários pré-configurados estarão disponíveis para acesso:

| Perfil | Usuário (`username`) | Senha | Descrição |
| :--- | :---: | :---: | :--- |
| 👑 **Diretor** | `dereck` | `123456` | Acesso irrestrito ao sistema, exclusão *Soft Delete*, gestão de lixeira, expurgo definitivo e cadastro/inativação de usuários. |
| 🛠️ **Administrador / TI** | `maciel` | `123456` | Operador técnico de campo. Cadastro e atualização de setores, equipamentos, monitores e periféricos. Sem acesso à exclusão ou lixeira. |

---

## 🧪 Execução de Testes

O projeto utiliza o **Pest PHP 4** para garantir a qualidade, cobertura de código e prevenção de regressões nas regras de negócio e permissões de acesso.

```bash
# Executar toda a suíte de testes com formatação compacta no terminal
php artisan test --compact

# Executar a suíte completa incluindo verificação de lint e análise de tipos estáticos
composer test

# Executar apenas uma classe ou arquivo de teste específico
php artisan test --compact --filter=EquipamentoFormTest

# Executar testes da funcionalidade de controle de acesso (ACL)
php artisan test --compact --filter=LixeiraManagerTest
```

---

## 📐 Diretrizes de Desenvolvimento

Todos os desenvolvedores que contribuem para o projeto devem seguir rigorosamente as diretrizes abaixo:

1. ### **Convenções de Models Eloquent**
   - Utilizar os atributos de classe do PHP 8.3/8.5 como `#[Fillable([...])]` e `#[Hidden([...])]` em vez de propriedades protegidas tradicionais.
   - Declarar explicitamente os tipos de retorno e tipagem de parâmetros em todos os métodos e relacionamentos Eloquent.
   - Incluir o trait `SoftDeletes` e `LogsActivity` em todas as entidades de negócio.

2. ### **Ciclo Rígido de Ordem de Desenvolvimento**
   Qualquer nova funcionalidade deve respeitar obrigatoriamente a seguinte sequência de criação:
   $$\text{1. Migrations} \longrightarrow \text{2. Models} \longrightarrow \text{3. Seeders/Factories} \longrightarrow \text{4. Routes} \longrightarrow \text{5. Livewire Components} \longrightarrow \text{6. Views (Blade)}$$

3. ### **Qualidade de Código e Formatação (Linter)**
   - Antes de efetuar qualquer commit, execute o linter oficial do projeto para garantir a conformidade de estilo:
   ```bash
   vendor/bin/pint --dirty --format agent
   ```
   - Verifique a ausência de erros de análise estática via Larastan:
   ```bash
   composer types:check
   ```

4. ### **Exemplo Prático de Padrão de Model**
   ```php
   <?php

   namespace App\Models;

   use Illuminate\Database\Eloquent\Attributes\Fillable;
   use Illuminate\Database\Eloquent\Factories\HasFactory;
   use Illuminate\Database\Eloquent\Model;
   use Illuminate\Database\Eloquent\Relations\BelongsTo;
   use Illuminate\Database\Eloquent\SoftDeletes;
   use Spatie\Activitylog\Models\Concerns\LogsActivity;
   use Spatie\Activitylog\Support\LogOptions;

   #[Fillable(['nome', 'created_by'])]
   class Setor extends Model
   {
       use HasFactory, LogsActivity, SoftDeletes;

       protected $table = 'setores';

       public function getActivitylogOptions(): LogOptions
       {
           return LogOptions::defaults()
               ->logAll()
               ->logOnlyDirty()
               ->dontLogEmptyChanges();
       }

       /**
        * Retorna o usuário responsável pelo cadastro do setor.
        *
        * @return BelongsTo<User, $this>
        */
       public function creator(): BelongsTo
       {
           return $this->belongsTo(User::class, 'created_by');
       }
   }
   ```

---

## 📁 Estrutura de Diretórios Relevantes

```text
controle-equipamentos/
├── app/
│   ├── Http/
│   │   └── Controllers/        # Controllers HTTP tradicionais (ex: PdfController para emissão de relatórios)
│   ├── Livewire/               # Componentes reativos do Livewire (Dashboard, EquipamentoForm, SetorManager, etc.)
│   ├── Models/                 # Models Eloquent com Attributes, SoftDeletes e LogsActivity
│   └── Policies/               # Policies do Laravel para validações de autorização e ACL por perfil
├── database/
│   ├── factories/              # Factories para geração de dados de teste (Equipamento, Monitor, Setor, User)
│   ├── migrations/             # Migrações do banco de dados (tabelas de usuários, equipamentos, monitores, etc.)
│   └── seeders/                # Seeders com usuários padrão (Diretor e Admin) e carga inicial de dados
├── lang/                       # Arquivos de tradução e localização do sistema (PT-BR)
├── resources/
│   ├── css/                    # Arquivos CSS e configurações do Tailwind CSS v4
│   └── views/
│       ├── livewire/           # Views Blade reativas correspondentes aos componentes Livewire
│       ├── pdf/                # Templates Blade formatados exclusivamente para geração de PDF
│       └── components/         # Layouts e componentes visuais reutilizáveis
├── routes/
│   └── web.php                 # Definição de rotas web, grupo de autenticação e middlewares de perfil
└── tests/
    ├── Feature/                # Testes de integração e fluxo das telas reativas em Pest PHP
    └── Unit/                   # Testes unitários isolados
```

---

## 🏷️ Controle de Versão da Aplicação

O versionamento da aplicação segue o padrão de **Semantic Versioning (SemVer)** e é controlado de forma centralizada:

1. ### **Localização da Versão no Código**
   A versão atual é configurada no arquivo de ambiente `.env`:
   ```env
   APP_VERSION=v1.0.1
   ```
   A variável é carregada nativamente pelo Laravel em `config/app.php` e injetada no rodapé da página de configurações (`Configuracoes.php`):
   ```html
   <footer class="text-xs text-slate-500 dark:text-slate-400">
       Versão do Sistema: {{ config('app.version') }}
   </footer>
   ```

2. ### **Procedimento de Atualização**
   Sempre que uma nova release ou correção for realizada:
   - Atualize o valor da chave `APP_VERSION` no arquivo `.env` e `.env.example`.
   - Atualize a badge de versão no topo deste arquivo `README.md`.

3. ### **Convenção Obrigatória de Mensagens de Commit**
   Todos os commits devem referenciar a versão no título da mensagem:
   - `feat(v1.0.1): adiciona filtro de ordenacao por data no cadastro de equipamentos`
   - `fix(v1.0.2): corrige validacao de serial duplicado no formulario de monitores`
   - `docs(v1.0.0): atualiza instrucoes de instalacao e tabela de dependencias no README`

---

## 🔒 Segurança & Conformidade (LGPD)

O sistema implementa rigorosos mecanismos de proteção de dados e segurança da informação:

- **Criptografia e Hashing de Senhas**: Todas as senhas de usuários são armazenadas com hash seguro via algoritmo **Bcrypt** através do cast `'password' => 'hashed'` no model `User`.
- **Controle de Acesso Baseado em Papéis (RBAC)**: Proteção de rotas e ações no backend através do middleware `role:diretor` e Policies (`UserPolicy`, `EquipamentoPolicy`, `SetorPolicy`, `PerifericoPolicy`), impedindo que usuários não autorizados executem chamadas diretas aos componentes.
- **Rastreabilidade e Trilha de Auditoria (LGPD)**: Qualquer criação, alteração ou exclusão armazena o usuário autor (`created_by`), os valores anteriores e atuais via `Spatie Activitylog`, permitindo auditoria detalhada de conformidade.
- **Proteção Contra Exclusão Acidental**: Implementação de *Soft Delete* nativo em todas as tabelas principais. A exclusão definitiva na lixeira exige confirmação por código em modal reativo.
- **Proteção Web Padrão Laravel**: Proteção nativa contra ataques CSRF (Cross-Site Request Forgery), sanitização automática de consultas via Eloquent ORM contra SQL Injection e escaping automático de dados no Blade contra XSS (Cross-Site Scripting).
