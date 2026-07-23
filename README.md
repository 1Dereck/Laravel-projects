# Projetos Laravel

Repositório dedicado a centralizar meus projetos desenvolvidos com **Laravel**.

Este espaço substitui parte do que antes ficava no repositório `PHP-student`, reunindo agora especificamente os projetos construídos com o framework, à medida que forem sendo desenvolvidos e organizados aqui.

---

## 📌 Status

✅ **Ativo** — Repositório em constante atualização. Contém projetos concluídos e em evolução.

---

## 🎯 Objetivo

- Reunir em um único lugar os projetos Laravel desenvolvidos para estudo, prática e modernização de sistemas.
- Servir como portfólio da evolução com o framework (Eloquent, Migrations, Controllers, Middleware, Gates/Policies, Repository Pattern, etc.).
- Facilitar a organização e consulta de projetos anteriores e novas aplicações.

---

## 🚀 Projetos no Repositório

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

- **Linguagem:** PHP 8.x
- **Framework:** Laravel 13
- **Banco de Dados:** MySQL / PostgreSQL
- **Front-end:** Tailwind CSS v4, Vite, JavaScript (Blade)
- **Testes & Qualidade:** Pest PHP, Laravel Pint

---

## 📂 Estrutura do Repositório

```text
Laravel-projects/
├── sistema-assistencia-social/    # Sistema de Gestão para População em Situação de Rua
└── README.md                       # Documentação principal do repositório
```

---

*Repositório em atualização constante conforme novos projetos Laravel forem desenvolvidos.*

