# Projetos Laravel

Repositório dedicado a centralizar meus projetos desenvolvidos com **Laravel**.

Este espaço substitui parte do que antes ficava no repositório `PHP-student`, reunindo agora especificamente os projetos construídos com o framework, à medida que forem sendo desenvolvidos e organizados aqui.

---

## 🎯 Objetivo

- Reunir em um único lugar os projetos Laravel desenvolvidos para estudo, prática e modernização de sistemas.
- Servir como portfólio da evolução com o framework (Eloquent, Migrations, Controllers, Middleware, Gates/Policies, Repository Pattern, etc.).
- Facilitar a organização e consulta de projetos anteriores e novas aplicações.

---

## 🚀 Projetos no Repositório

### 📦 [Sistema de Inventário TI (`/controle-equipamentos`)](./controle-equipamentos)

Sistema web reativo corporativo desenvolvido para digitalizar, padronizar e gerenciar o levantamento do parque tecnológico (computadores, monitores e periféricos) alocados por secretarias e departamentos da equipe de TI.

- **Tecnologias:** Laravel 13, PHP 8.5, Livewire v4, Tailwind CSS v4, Spatie Activitylog, DomPDF, Pest v4, Pint, Larastan, MySQL
- **Principais Funcionalidades:**
  - **Controle de Acesso por Perfis (ACL):** Permissões distintas para *Diretor* (acesso total, gestão de usuários e expurgo/restauração de lixeira) e *Administrador / TI* (operação de campo e cadastros).
  - **Gerenciamento Reativo de Equipamentos & Setores:** Cadastro dinâmico de secretarias/setores, computadores (desktops/notebooks), monitores múltiplos e periféricos com reatividade full-stack via Livewire v4.
  - **Trilha de Auditoria Transparente:** Registro detalhado de alterações em tempo real (`spatie/laravel-activitylog`) com visualização em modal de linha do tempo.
  - **Exclusão Segura & Lixeira:** Suporte a Soft Deletes com painel exclusivo de lixeira e confirmação por palavra-chave para exclusão definitiva.
  - **Relatórios PDF & Dashboard Gerencial:** Geração de relatórios consolidados por setor em PDF (`barryvdh/laravel-dompdf`) e dashboard gerencial em tempo real com estatísticas e gráficos.

👉 Acesse a [documentação completa do Sistema de Inventário TI](./controle-equipamentos/README.md) para detalhes de arquitetura e como rodar o projeto localmente.

---

### 🏢 [Sistema de Acolhimento (`/sistema-assistencia-social`)](./sistema-assistencia-social)

Modernização e migração completa de um sistema legado em PHP puro para **Laravel 13** e **PHP 8.5**. Trata-se de um sistema de gestão para população em situação de rua e extrema vulnerabilidade social, em conformidade com as diretrizes da LGPD.

- **Tecnologias:** Laravel 13, PHP 8.5, Tailwind CSS v4, Pest v4, Pint, MySQL
- **Principais Funcionalidades:**
  - **Autenticação & Controle de Acesso (RBAC):** Perfis de *Diretor*, *Administrador* e *Usuário*, com controle estrito por Gates/Policies e mascaramento de CPF para privacidade (LGPD).
  - **Busca Otimizada:** Pesquisa unificada por Nome, CPF ou RG com paginação.
  - **Integração com Câmera/Webcam:** Captura de foto de perfil via navegador (`MediaDevices`) e upload de arquivos multimídia.
  - **Dossiê PDF:** Exportação de relatórios individuais de histórico e evoluções técnicas do acolhido.
  - **Ocultação Lógica (Soft Hiding):** Inativação de cadastros sem perda do histórico no banco de dados.

👉 Acesse a [documentação completa do Sistema de Acolhimento](./sistema-assistencia-social/README.md) para detalhes de arquitetura e como rodar o projeto localmente.

---

## 🛠️ Tecnologias Gerais

- **Linguagem:** PHP 8.x (PHP 8.5)
- **Framework:** Laravel 13
- **Full-Stack Reativo:** Livewire v4
- **Banco de Dados:** MySQL / PostgreSQL
- **Front-end:** Tailwind CSS v4, Vite, JavaScript (Blade)
- **Testes & Qualidade:** Pest PHP, Laravel Pint, Larastan

---

## 📂 Estrutura do Repositório

```text
Laravel-projects/
├── controle-equipamentos/         # Sistema de Inventário TI
├── sistema-assistencia-social/    # Sistema de Gestão para População em Situação de Rua
└── README.md                       # Documentação principal do repositório
```

---

*Repositório em atualização constante conforme novos projetos Laravel forem desenvolvidos.*

