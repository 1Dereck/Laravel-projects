# 🗺️ Roadmap e Planejamento: Laravel Moderno na Prática

> **Objetivo:** Reaprender e dominar os recursos modernos do ecossistema Laravel (PHP 8+, Laravel 11/12+, Livewire, Enums, Casts, Policies e Arquitetura Limpa) de forma 100% prática, guiada e sem decoreba.

---

## 🎯 Por que estamos fazendo isso?

Você já tem bagagem e entende o ciclo básico (Migrations, Models, Controllers e Views). Porém, com a evolução do PHP e do Laravel nos últimos anos:
1. Muitas soluções que antes eram feitas com "gambiarras" ou lógicas espalhadas agora têm **recursos nativos elegantes** (ex: Enums nativos do PHP 8.1+, Casts tipados, método `casts()`, Policies limpas).
2. O front-end integrado ao back-end mudou drasticamente com o **Livewire**, eliminando a necessidade de APIs REST complexas para aplicações interativas.
3. Não queremos apenas "escrever código", mas entender:
   - **Por que essa ferramenta foi criada?**
   - **Qual dor ou problema do código antigo ela resolve?**
   - **Como usá-la com confiança no dia a dia e no trabalho?**

---

## 🏗️ O Projeto Prático: **"TaskForge" (Gestão Inteligente de Projetos e Chamados)**

Para aplicar tudo sem ficar em exemplos abstratos ("Foo/Bar"), vamos construir um projeto real e completo:

### 🧩 Funcionalidades que cobrem todos os tópicos modernos:
1. **Controle de Usuários e Perfis:** Administrador, Gerente de Projeto, Desenvolvedor e Cliente. *(Enums + Policies + Gates)*.
2. **Projetos e Tarefas:** 
   - Status de tarefas com transições de estado permitidas *(Enums com métodos e regras de negócio)*.
   - Metadados, logs de alteração e configurações em JSON tratadas como Objetos/Collections nativas *(Casts Avançados `AsArrayObject` / `AsCollection`)*.
   - Informações sensíveis com criptografia automática *(Casts `encrypted`)*.
3. **Regras de Acesso e Permissões:**
   - Quem pode criar tarefas, alterar status, delegar, comentar ou excluir *(Policies modernas + autorização em controllers e componentes Livewire)*.
4. **Interface Reativa em Tempo Real com Livewire:**
   - Quadro Kanban / Lista de Tarefas interativa sem recarregar a página (`wire:model`, `wire:click`, Livewire Actions).
   - Filtros dinâmicos e paginação instantânea em tempo real.
   - Modais de criação/edição e validação em tempo de digitação.
   - Disparo de eventos entre componentes e notificações toast.
5. **Testes Automatizados com Pest:**
   - Testes simples, expressivos e modernos garantindo que nossas Policies, Enums e Livewire funcionem perfeitamente.

---

## 📚 Estrutura da Documentação Criada

A pasta `documentation/` está organizada nos seguintes guias modulares:

| Arquivo | Conteúdo Principal |
| :--- | :--- |
| [`00-ROADMAP-E-PLANEJAMENTO.md`](file:///C:/1Projetos-GitHub-Desktop/Aprendizado/project-practicing/documentation/00-ROADMAP-E-PLANEJAMENTO.md) | Visão geral, mentalidade de aprendizado e cronograma. |
| [`01-PHP-ENUMS-NO-LARAVEL.md`](file:///C:/1Projetos-GitHub-Desktop/Aprendizado/project-practicing/documentation/01-PHP-ENUMS-NO-LARAVEL.md) | Enums do PHP 8.1+, Backed Enums, Casts em Models, métodos e validação. |
| [`02-CASTS-AVANCADOS-ELOQUENT.md`](file:///C:/1Projetos-GitHub-Desktop/Aprendizado/project-practicing/documentation/02-CASTS-AVANCADOS-ELOQUENT.md) | Novo método `casts()`, JSON casts (`AsArrayObject`), encriptação e Custom Casts. |
| [`03-AUTORIZACAO-GATES-E-POLICIES.md`](file:///C:/1Projetos-GitHub-Desktop/Aprendizado/project-practicing/documentation/03-AUTORIZACAO-GATES-E-POLICIES.md) | Gates vs Policies, regras contextuais, `@can`, autorização no Livewire. |
| [`04-ARQUITETURA-MODERNA-LARAVEL.md`](file:///C:/1Projetos-GitHub-Desktop/Aprendizado/project-practicing/documentation/04-ARQUITETURA-MODERNA-LARAVEL.md) | Form Requests, Single Action Controllers, Action Classes, Scopes e Observers. |
| [`05-LIVEWIRE-DO-ZERO-AO-AVANCADO.md`](file:///C:/1Projetos-GitHub-Desktop/Aprendizado/project-practicing/documentation/05-LIVEWIRE-DO-ZERO-AO-AVANCADO.md) | Ciclo de vida, Two-Way Binding, Ações, Validação em Tempo Real e Eventos. |
| [`06-PROJETO-PRATICO-PASSO-A-PASSO.md`](file:///C:/1Projetos-GitHub-Desktop/Aprendizado/project-practicing/documentation/06-PROJETO-PRATICO-PASSO-A-PASSO.md) | Guia prático passo a passo de desenvolvimento guiado do projeto TaskForge. |
| [`07-PEST-PHP-TESTES-MODERNOS.md`](file:///C:/1Projetos-GitHub-Desktop/Aprendizado/project-practicing/documentation/07-PEST-PHP-TESTES-MODERNOS.md) | Testes modernos com Pest PHP a cada etapa (TDD e testes contínuos). |
| [`08-PADROES-E-BOAS-PRATICAS.md`](file:///C:/1Projetos-GitHub-Desktop/Aprendizado/project-practicing/documentation/08-PADROES-E-BOAS-PRATICAS.md) | Guia definitivo de quando usar cada recurso (Obrigatórios vs Sob Demanda). |

---

## 🤝 Como vamos trabalhar juntos (Método Guiado)

1. **Passo a Passo Sem Pressa:** Vamos fazer um módulo por vez.
2. **Explicação do "Porquê":** Antes de escrever qualquer linha de código, eu explicarei o motivo pelo qual usamos aquela abordagem em vez da antiga.
3. **Você no Controle:** Eu mostro a estrutura, você implementa (ou pede exemplos), testa, erra, ajusta e faz perguntas.
4. **Sem Medo de Perguntar:** Pode perguntar "o que significa esse `?->`?", "por que colocou esse `#[Validate]`?", "qual a diferença de `$casts` para o método `casts()`?".
5. **Revisão e Validação:** Em cada etapa, validamos a execução no navegador e com testes automatizados.
