# 🛠️ Roteiro Prático Passo a Passo: Construindo o TaskForge

> **Instruções:** Este roteiro será executado juntos em nossas sessões. Em cada etapa, vamos discutir a teoria, entender o motivo de cada escolha e implementar o código com você no comando.

---

## 📋 Checklist de Fases do Projeto

- [x] **Fase 1: Enums do PHP 8.1+, Métodos de Domínio e Testes Unitários com Pest**
- [x] **Fase 2: Banco de Dados, Migrations Modernas e Testes de Schema com Pest**
- [ ] **Fase 3: Models Eloquent com Casts Avançados, Scopes e Testes com Pest**
- [ ] **Fase 4: Factories, Seeders Realistas e Testes de Criação**
- [ ] **Fase 5: Camada de Segurança: Gates, Policies e Testes de Autorização (403/200)**
- [ ] **Fase 6: Actions, Form Requests e Testes de Regra de Negócio**
- [ ] **Fase 7: Componentes Reativos com Livewire e Testes de Interatividade/Validação**
- [ ] **Fase 8: Jobs/Filas, Events/Listeners e Verificação Estática com Larastan e Pint**

---

## 🚀 Detalhamento das Etapas com Testes Contínuos

### 📌 Fase 1: Enums do PHP 8.1+ e Testes de Domínio
**O que vamos construir em `app/Enums/`:**
- `UserRole.php`: Papéis (`Admin`, `ProjectManager`, `Developer`, `Client`) + métodos de labels, cores e regras.
- `TaskStatus.php`: Status (`Backlog`, `InProgress`, `InReview`, `Completed`, `Cancelled`) + métodos de transição permitida.
- `TaskPriority.php`: Prioridades (`Low`, `Medium`, `High`, `Urgent`).
**🧪 Teste Pest imediato:**
- Criar `tests/Unit/Enums/UserRoleTest.php` e `tests/Unit/Enums/TaskStatusTest.php` testando labels, valores e transições permitidas.

---

### 📌 Fase 2: Banco de Dados, Migrations Modernas e Testes de Schema
**O que vamos construir:**
- Ajustar migration de `users` com coluna `role` utilizando `->default(UserRole::Client->value)`.
- Criar migrations para `projects` (`settings` JSON, `api_secret` criptografado) e `tasks` (`metadata` JSON, `deadline_at`, `status` default `TaskStatus::Backlog->value`).
**🧪 Teste Pest imediato:**
- Criar `tests/Feature/Database/MigrationsTest.php` para testar se as tabelas e colunas sobem sem conflito no banco SQLite de testes.

---

### 📌 Fase 3: Models Eloquent, Casts Avançados e Scopes
**O que vamos construir em `app/Models/`:**
- `User`, `Project`, `Task` com método `casts()` (`AsArrayObject`, `encrypted`, Enums, `immutable_datetime`).
- Local scopes: `scopeOverdue()`, `scopeOfPriority()`.
**🧪 Teste Pest imediato:**
- Criar `tests/Feature/Models/TaskTest.php` testando mutação de JSON in-place (`AsArrayObject`), criptografia e filtragem via Scopes.

---

### 📌 Fase 4: Factories e Seeders
**O que vamos construir em `database/factories/` e `database/seeders/`:**
- Factories expressivas usando os Enums (`Task::factory()->urgent()->create()`).
- DatabaseSeeder com dados realistas.
**🧪 Teste Pest imediato:**
- Testar se os seeders geram dados consistentes no banco.

---

### 📌 Fase 5: Policies, Gates e Autorização
**O que vamos construir em `app/Policies/`:**
- `TaskPolicy` e `ProjectPolicy` com checagem de Enums e respostas customizadas (`Response::deny(...)`).
**🧪 Teste Pest imediato:**
- Criar `tests/Feature/Policies/TaskPolicyTest.php` testando que Admins podem tudo, donos podem editar, e terceiros tomam 403.

---

### 📌 Fase 6: Form Requests e Action Classes
**O que vamos construir:**
- `CreateTaskAction` com `DB::transaction`.
- `StoreTaskRequest` com `Rule::enum()`.
**🧪 Teste Pest imediato:**
- Testar a execução isolada da Action e as regras de validação do Request.

---

### 📌 Fase 7: Componentes Reativos com Livewire
**O que vamos construir em `app/Livewire/`:**
- `TaskList` / `ProjectBoard`: busca em tempo real com debounce, filtros de status, formulários de criação rápida com `#[Validate]`.
**🧪 Teste Pest imediato:**
- Criar `tests/Feature/Livewire/TaskListTest.php` testando busca, erros de validação e emissão de eventos sem abrir o navegador.

---

### 📌 Fase 8: Qualidade Total (Larastan + Pint + Pest Suite)
- Rodar toda a suíte de testes (`php artisan test`).
- Rodar análise estática de tipos (`composer types:check` / `phpstan`).
- Rodar o linter de formatação de código (`composer lint` / `pint`).

---

## 🏁 Pronto para começar?
Quando estiver pronto para iniciar a **Fase 1**, basta avisar e daremos o primeiro passo juntos!
