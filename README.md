# Projetos Laravel

Repositório dedicado a centralizar meus projetos desenvolvidos com **Laravel**.

Este espaço substitui parte do que antes ficava no repositório `PHP-student`, reunindo agora especificamente os projetos construídos com o framework, à medida que forem sendo desenvolvidos e organizados aqui.

---

## 🎯 Objetivo

- Reunir em um único lugar os projetos Laravel desenvolvidos para estudo, prática e modernização de sistemas.
- Servir como portfólio da evolução com o framework (Eloquent, Migrations, Controllers, Middleware, Gates/Policies, Repository Pattern, Livewire, etc.).
- Facilitar a organização e consulta de projetos anteriores e novas aplicações.

---

## 📊 Visão Geral dos Projetos & Versões

| Projeto | Diretório | Versão PHP | Versão Laravel | Frontend & UI | Principais Destaques |
| :--- | :--- | :---: | :---: | :--- | :--- |
| **Sistema de Inventário TI** | [`/controle-equipamentos`](./controle-equipamentos) | `PHP 8.5` (`^8.3`) | `Laravel 13.17` | Livewire v4, Tailwind v4 | ACL (Diretor/Admin), Auditoria, Lixeira, Relatórios PDF |
| **Hospitais Asclépio — Inventário** | [`/hospital-asclépio-inventario`](./hospital-asclépio-inventario) | `PHP 8.5` (`^8.3`) | `Laravel 13.17` | Livewire v4, Flux UI, Tailwind v4 | Auto-retroalimentação, 4 Perfis ACL, Monitores Múltiplos, Expurgo Seguro |
| **Sistema de Acolhimento** | [`/sistema-assistencia-social`](./sistema-assistencia-social) | `PHP 8.5` (`^8.3`) | `Laravel 13.8` | Livewire v4, Tailwind v4 | Migração de legado, Proteção LGPD, Dossiê PDF, Integração Webcam |

---

## 🚀 Projetos no Repositório

### 📦 [Sistema de Inventário TI (`/controle-equipamentos`)](./controle-equipamentos)

![PHP](https://img.shields.io/badge/PHP-8.5-blue?style=flat-square&logo=php)
![Laravel](https://img.shields.io/badge/Laravel-v13.17-red?style=flat-square&logo=laravel)
![Livewire](https://img.shields.io/badge/Livewire-v4.1-pink?style=flat-square&logo=livewire)

Sistema web reativo corporativo desenvolvido para digitalizar, padronizar e gerenciar o levantamento do parque tecnológico (computadores, monitores e periféricos) alocados por secretarias e departamentos da equipe de TI.

- **Versão PHP:** `PHP 8.5` (compatível com PHP 8.3+)
- **Versão Laravel:** `Laravel v13.17`
- **Stack Tecnológica:** Livewire v4, Tailwind CSS v4, Spatie Activitylog, DomPDF, Pest v4, Pint, Larastan, MySQL
- **Principais Funcionalidades:**
  - **Controle de Acesso por Perfis (ACL):** Permissões distintas para *Diretor* (acesso total, gestão de usuários e expurgo/restauração de lixeira) e *Administrador / TI* (operação de campo e cadastros).
  - **Gerenciamento Reativo de Equipamentos & Setores:** Cadastro dinâmico de secretarias/setores, computadores (desktops/notebooks), monitores múltiplos e periféricos com reatividade full-stack via Livewire v4.
  - **Trilha de Auditoria Transparente:** Registro detalhado de alterações em tempo real (`spatie/laravel-activitylog`) com visualização em modal de linha do tempo.
  - **Exclusão Segura & Lixeira:** Suporte a Soft Deletes com painel exclusivo de lixeira e confirmação por palavra-chave para exclusão definitiva.
  - **Relatórios PDF & Dashboard Gerencial:** Geração de relatórios consolidados por setor em PDF (`barryvdh/laravel-dompdf`) e dashboard gerencial em tempo real com estatísticas e gráficos.

👉 Acesse a [documentação completa do Sistema de Inventário TI](./controle-equipamentos/README.md) para detalhes de arquitetura e como rodar o projeto localmente.

---

### 🏥 [Sistema de Inventário Hospitais Asclépio (`/hospital-asclépio-inventario`)](./hospital-asclépio-inventario)

![PHP](https://img.shields.io/badge/PHP-8.5-blue?style=flat-square&logo=php)
![Laravel](https://img.shields.io/badge/Laravel-v13.17-red?style=flat-square&logo=laravel)
![Livewire](https://img.shields.io/badge/Livewire-v4.1-pink?style=flat-square&logo=livewire)
![Flux UI](https://img.shields.io/badge/Flux_UI-v2.15-06B6D4?style=flat-square)

Sistema web reativo corporativo voltado à gestão de infraestrutura de TI em redes hospitalares. Destaca-se pela sua arquitetura inteligente de auto-retroalimentação de dados, onde as interações do dia a dia expandem e vinculam automaticamente a estrutura orgânica da organização.

- **Versão PHP:** `PHP 8.5` (compatível com PHP 8.3+)
- **Versão Laravel:** `Laravel v13.17`
- **Stack Tecnológica:** Livewire v4, Flux UI, Tailwind CSS v4, Spatie Activitylog, DomPDF, Pest v4, Pint, Larastan, MySQL
- **Principais Funcionalidades:**
  - **Arquitetura de Auto-Retroalimentação de Dados:** Expansão e vinculação automática da rede de dados (Usuários $\rightarrow$ Locais $\rightarrow$ Setores $\rightarrow$ Secretarias/Unidades $\rightarrow$ Equipamentos), eliminando cadastros redundantes.
  - **Controle de Acesso em 4 Perfis (ACL):**
    - 👑 *Diretor*: Visão macro total, expurgo definitivo da lixeira (*Hard Delete*) e relatórios consolidados.
    - 🛡️ *Administrador / TI*: Operação de campo, cadastro de equipamentos/setores e arquivamento (*Soft Delete*).
    - 👔 *Coordenador*: Gestão setorial, criação de usuários com vinculação automática ao seu local/setor.
    - 👤 *Usuário*: Consulta operacional delimitada ao seu setor.
  - **Gestão Avançada de Hardware & Monitores Múltiplos:** Cadastro detalhado de desktops e notebooks (com perfil de desempenho: *básico*, *intermediário*, *avançado*), vinculação reativa de múltiplos monitores por estação de trabalho e gestão de periféricos avulsos ou atrelados.
  - **Trilha de Auditoria & Lixeira Controlada:** Auditoria contínua de alterações (`spatie/laravel-activitylog`) com visualização em linha do tempo e lixeira segura com confirmação por palavra-chave `"CONFIRMAR"` para expurgos.
  - **Dashboard Gerencial & Exportação PDF:** Dashboard dinâmico com métricas, ranking Top 10 de equipamentos por setor e emissão de relatórios oficiais em PDF (`barryvdh/laravel-dompdf`).

👉 Acesse a [documentação completa do Hospitais Asclépio](./hospital-asclépio-inventario/README.md) para detalhes de arquitetura e instruções de instalação.

---

### 🏢 [Sistema de Acolhimento (`/sistema-assistencia-social`)](./sistema-assistencia-social)

![PHP](https://img.shields.io/badge/PHP-8.5-blue?style=flat-square&logo=php)
![Laravel](https://img.shields.io/badge/Laravel-v13.8-red?style=flat-square&logo=laravel)
![Livewire](https://img.shields.io/badge/Livewire-v4.3-pink?style=flat-square&logo=livewire)

Modernização e migração completa de um sistema legado em PHP puro para **Laravel 13** e **PHP 8.5**. Trata-se de um sistema de gestão para população em situação de rua e extrema vulnerabilidade social, em conformidade com as diretrizes da LGPD.

- **Versão PHP:** `PHP 8.5` (compatível com PHP 8.3+)
- **Versão Laravel:** `Laravel v13.8`
- **Stack Tecnológica:** Livewire v4, Tailwind CSS v4, Pest v4, Pint, MySQL
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
- **Framework:** Laravel 13 (v13.8 - v13.17)
- **Full-Stack Reativo:** Livewire v4, Flux UI
- **Banco de Dados:** MySQL / PostgreSQL
- **Front-end:** Tailwind CSS v4, Vite, JavaScript (Blade)
- **Testes & Qualidade:** Pest PHP, Laravel Pint, Larastan

---

## 📂 Estrutura do Repositório

```text
Laravel-projects/
├── controle-equipamentos/         # Sistema de Inventário TI
├── hospital-asclépio-inventario/  # Sistema de Inventário Hospitais Asclépio
├── sistema-assistencia-social/    # Sistema de Gestão para População em Situação de Rua
└── README.md                       # Documentação principal do repositório
```

---

*Repositório em atualização constante conforme novos projetos Laravel forem desenvolvidos.*
