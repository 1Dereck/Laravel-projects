# 🏢 Sistema de Acolhimento — Sistema de Gestão para População em Situação de Rua

[![Laravel Version](https://img.shields.io/badge/Laravel-13.17.0-FF2D20?logo=laravel&logoColor=white)](https://laravel.com)
[![PHP Version](https://img.shields.io/badge/PHP-8.5.8-777BB4?logo=php&logoColor=white)](https://php.net)
[![Tailwind CSS Version](https://img.shields.io/badge/Tailwind_CSS-v4-38B2AC?logo=tailwindcss&logoColor=white)](https://tailwindcss.com)
[![Pest Testing](https://img.shields.io/badge/Pest-v4-FF4081?logo=pest&logoColor=white)](https://pestphp.com)
[![Pint Linter](https://img.shields.io/badge/Pint-v1-4F5B93?logo=laravel&logoColor=white)](https://laravel.com)

Este repositório contém a modernização e migração do sistema legado em PHP puro para **Laravel Framework 13.17.0** com **PHP versão 8.5.8** do **Sistema de Acolhimento**.

O sistema gerencia acolhimentos, atendimentos, evoluções técnicas, uploads de mídias/documentos e o histórico de cadastros de pessoas em situação de extrema vulnerabilidade social, garantindo total controle de permissões e em conformidade com as diretrizes da LGPD (Lei Geral de Proteção de Dados).

---

## 📌 Funcionalidades Concluídas (Escopo Central Migrado e Aprimorado)

O desenvolvimento da migração foi **concluído com sucesso**, trazendo melhorias drásticas de segurança, usabilidade, performance e qualidade de código:

### 1. Autenticação & Perfis de Acesso Rígidos (Role-Based Access Control)

O sistema implementa regras estritas de acesso baseado em 3 perfis mapeados nos Gates/Policies do Laravel:

- **Diretor (`d`):** Nível administrativo supremo. Pode criar e gerenciar usuários de todos os níveis, excluir contas operacionais definitivamente com confirmação visual em tela e visualizar todos os dados irrestritos.
- **Administrador (`a`):** Nível de coordenação e equipe técnica social. Pode cadastrar e editar acolhidos, enviar fotos/documentos e criar contas de usuários. Não tem permissão para excluir usuários ou visualizar outros perfis administrativos/Diretor.
- **Usuário (`n`):** Nível de consulta. Possui visualização restrita.
    - **Mascaramento de CPF:** O CPF é exibido como `***.***.***-**` para proteger dados sensíveis.
    - **Exceção de Acesso:** O Usuário só visualiza dados sigilosos e o CPF completo se for explicitamente o **Técnico Responsável** designado para aquele acolhido ou tiver a flag `tipo_acesso = 's'` (Sigiloso).

### 2. Busca Otimizada e Controle de Cadastro

- **Busca Unificada:** A barra de pesquisa na listagem de acolhidos permite localizar registros por **Nome, CPF ou RG** de forma rápida e paginada (15 registros por página).
- **Fluxos Independentes:** O fluxo de busca é totalmente isolado do fluxo de criação, eliminando o comportamento legado confuso e oferecendo um botão claro de `+ Novo Cadastro`.
- **Ocultação Lógica (Soft Hiding):** Permite inativar cadastros (`acolhimento.oculto = 's'`) sem a exclusão física do registro no banco de dados. Registros inativos só aparecem em buscas específicas, com a indicação visual de "Ocultado".

### 3. RG & CPF com Validação e Máscara Dinâmica

- **Máscaras no Front-end:** Máscara visual no padrão `000.000.000-00` para o CPF. Máscara adaptativa para RG de 8 dígitos (`1.234.567-8`) e 9 dígitos (`12.345.678-9`).
- **Armazenamento Limpo:** Sanitização automática no backend de caracteres especiais (pontuações, traços) ao salvar ou atualizar no banco de dados MySQL, mantendo a integridade e a compatibilidade do schema legado.

### 4. Multimídia: Câmera Integrada e Upload

- **Captura via Webcam:** Integração direta com a API `MediaDevices` do navegador para captura de fotos de identificação em tempo real pelo operador na hora do atendimento.
- **Upload Tradicional:** Opção para envio de arquivos de imagem locais.
- **Otimização de Armazenamento:** Regra estrita de foto única. Ao atualizar a foto de um acolhido, a imagem anterior é automaticamente removida do storage (`storage/app/public/fotos/`).

### 5. Dossiê PDF e Controle de Arquivos/Anotações

- **Exportação de Dossiê:** Geração de relatórios individuais em PDF contendo o histórico do acolhido e suas evoluções técnicas com a identidade visual do Sistema de Acolhimento.
- **Nível de Sigilo:** Permite registrar evoluções/observações normais e sigilosas, bem como anexar documentos protegidos, onde apenas usuários autorizados ou o Técnico Responsável possuem visibilidade do conteúdo.

### 6. Interface Moderna & Customizável

- **Identidade Visual:** Paleta de cores moderna e institucional adaptada para o Sistema de Acolhimento.
- **Botões Verdes Sólidos:** Botões de ação em verde sólido (`bg-emerald-600` / `hover:bg-emerald-700` no modo claro, `dark:bg-emerald-500` / `dark:hover:bg-emerald-600` no modo escuro) sem uso de degradês, garantindo conformidade com a identidade corporativa do sistema.
- **Modo Escuro / Claro:** Chaveador de tema nas configurações do usuário, com preferência persistida na Sessão (`session`).

---

## 🛠️ Stack Tecnológica & Versões

Para garantir a sustentabilidade de longo prazo deste sistema e facilitar a manutenção futura, abaixo estão especificadas as versões exatas de todas as dependências principais que compõem o ecossistema da aplicação:

### Core do Backend
| Tecnologia / Pacote | Versão Requerida (`composer.json`) | Versão do Ambiente Local | Finalidade / Descrição |
| :--- | :--- | :--- | :--- |
| **PHP** | `^8.3` | `8.5.8` | Linguagem base da aplicação. |
| **Laravel Framework** | `^13.8` | `13.17.0` | Framework web estrutural da aplicação. |
| **Laravel Tinker** | `^3.0` | `3.0.x` | Console iterativo de comandos PHP (REPL). |
| **MySQL** | - | `8.0+` | Banco de dados (utilizando schema legado imutável). |

### Ecossistema de Desenvolvimento & Linter
| Pacote | Versão Requerida (`composer.json`) | Finalidade / Descrição |
| :--- | :--- | :--- |
| **Laravel Pail** | `^1.2.5` | Visualizador em tempo real dos logs da aplicação. |
| **Laravel Pint** | `^1.27` | Linter e formatador de código opinativo para PHP. |
| **Collision** | `^8.6` | Tratamento e exibição de erros no terminal durante os testes. |

### Suite de Testes
| Pacote | Versão Requerida (`composer.json`) | Finalidade / Descrição |
| :--- | :--- | :--- |
| **Pest PHP** | `^4.7` | Framework de testes principal da aplicação (sintaxe expressiva). |
| **Pest Plugin Laravel** | `^4.1` | Integração nativa do Pest com o ecossistema Laravel. |
| **FakerPHP / Faker** | `^1.23` | Gerador de dados fictícios para as Factories e Seeders. |
| **Mockery** | `^1.6` | Framework para criação de mocks e dublês de testes PHP. |

### Core do Frontend
| Tecnologia / Pacote | Versão Requerida (`package.json`) | Finalidade / Descrição |
| :--- | :--- | :--- |
| **Tailwind CSS** | `^4.0.0` | Framework utilitário de estilização CSS. |
| **Vite** | `^8.0.0` | Build tool e empacotador de assets do frontend. |
| **@tailwindcss/vite** | `^4.0.0` | Plugin oficial do Tailwind CSS v4 para compilação via Vite. |
| **Laravel Vite Plugin** | `^3.1` | Integração do Vite com o roteamento/Blade do Laravel. |
| **Concurrently** | `^9.0.1` | Execução simultânea de comandos no ambiente de desenvolvimento. |

---

## ⚙️ Instalação e Configuração

Para executar o projeto localmente, certifique-se de ter instalado em sua máquina o **PHP versão 8.5.8** (ou superior), o **Composer**, o **Node.js** e o **MySQL**.

### 1. Clonar o Repositório e Instalar Dependências

```bash
composer install
npm install
```

### 2. Configurar Variáveis de Ambiente

Copie o arquivo `.env.example` para `.env` e ajuste as credenciais do seu banco de dados local:

```bash
copy .env.example .env
```

No arquivo `.env`, altere:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sistema_acolhimento
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
```

### 3. Chave da Aplicação e Link de Armazenamento

Gere a chave criptográfica do Laravel e crie o link simbólico para as fotos/documentos de upload:

```bash
php artisan key:generate
php artisan storage:link
```

### 4. Executar Migrations e Popular Banco (Seeds)

Execute as migrações e popule as tabelas com os estados brasileiros e usuários padrão para testes:

```bash
php artisan migrate --seed
```

### 5. Compilar o Frontend e Rodar o Servidor

Inicie o servidor de desenvolvimento do Laravel e compile os assets do Tailwind CSS v4:

```bash
# Terminal 1: Iniciar o backend
php artisan serve

# Terminal 2: Compilar os assets em tempo de desenvolvimento
npm run dev
```

Acesse o sistema no endereço padrão: [http://localhost:8000](http://localhost:8000).

---

## 🔑 Credenciais Padrão (Ambiente de Testes)

Após executar o seed do banco de dados, você poderá efetuar o login utilizando as seguintes contas de teste:

| Perfil                | Usuário / Login | Senha    | Descrição                                                                                         |
| :-------------------- | :-------------- | :------- | :------------------------------------------------------------------------------------------------ |
| **Diretor**           | `dereck`        | `123456` | Acesso supremo. Gerenciamento e exclusão de usuários, edição total de acolhidos.                  |
| **Administrador**     | `luiz`          | `123456` | Gerenciamento de acolhidos, upload de mídias/câmera e criação de novos usuários.                  |
| **Usuário**           | `fagner`        | `123456` | Apenas consulta. CPF mascarado e dados sigilosos ocultados (exceto se for o Técnico Responsável). |

---

## 🧪 Execução de Testes

A suite de testes utiliza o **Pest PHP** para validar a integridade de rotas, permissões, uploads e mascaramento de dados.

Para rodar todos os testes automatizados da aplicação:

```bash
php artisan test --compact
```

---

## 🛠️ Diretrizes de Desenvolvimento (Regras do Legado)

Ao criar ou modificar Models, Migrations, Controllers ou Views, todos os desenvolvedores devem seguir estritamente estas diretrizes para não quebrar a compatibilidade com o banco de dados legado:

1. **Configuração de Models para Banco Legado:**
   - O Laravel por padrão espera as colunas `created_at` e `updated_at`. Como o banco legado não possui essa estrutura (e as tabelas gerenciam datas por `DEFAULT current_timestamp()`), você **deve desativar os timestamps automáticos** em todas as Models legadas:
     ```php
     public $timestamps = false;
     ```
   - Defina explicitamente o nome da tabela e da chave primária (já que não seguem o padrão pluralizado em inglês do Laravel):
     ```php
     protected $table = 'nome_tabela_legada';
     protected $primaryKey = 'id_chave_primaria';
     ```

2. **Sanitização de Dados:**
   - **CPF e RG:** Sempre remova a formatação dos campos CPF e RG antes de salvá-los no banco (salvar apenas números), garantindo consistência com o banco antigo. O CPF é obrigatório (`required`) e o RG é opcional (`nullable`).
   - **Datas Sentinel:** Quando o banco legado usar valores padrão sentinel para datas não informadas, utilize o padrão `1900-01-01`.

3. **Padronização de Código (Laravel Pint):**
   - O projeto utiliza o **Laravel Pint** para manter o estilo de código consistente.
   - Antes de realizar qualquer commit, execute o linter para formatar os arquivos PHP que você modificou:
     ```bash
     vendor/bin/pint --dirty
     ```

---

## 📁 Estrutura de Diretórios Relevantes

- `app/Http/Controllers/` - Contém os controladores da aplicação (`AcolhimentoController`, `ConfiguracaoController`, `ObservacaoController` e `UserController`).
- `app/Models/` - Modelagem das tabelas do banco de dados, contendo relacionamentos Eloquent e mutators para limpeza/máscara de RG/CPF (`Acolhimento`, `User`, `Observacao`, etc.).
- `database/migrations/` - Arquivos de migração estruturados para replicar o banco legado e implementar as melhorias de escopo autorizadas.
- `resources/views/` - Templates Blade estilizados sob medida com Tailwind CSS v4.
- `tests/Feature/` - Suite de testes contendo validações dos perfis, webcam, upload e permissões.

---

## 🏷️ Controle de Versão da Aplicação

A versão da aplicação é exibida no rodapé da página de configurações e serve para acompanhar a evolução do projeto. Ela é gerenciada diretamente e de forma estática nas configurações do Laravel, no arquivo [config/app.php](config/app.php):

1. **Configuração Principal:** Definida sob a chave `'version'`:
   ```php
   'version' => 'v1.0.0',
   ```

2. **Como Atualizar a Versão:**
   - **No Código:** Abra o arquivo [config/app.php](config/app.php), localize a chave `'version'` e altere o valor da string para a nova versão (ex: `'v1.0.1'`).
   - **Limpeza do Cache:** Caso o cache de configurações do Laravel esteja ativado em seu ambiente, execute o seguinte comando no terminal para aplicar a mudança no navegador:
     ```bash
     php artisan config:clear
     ```

3. **Fluxo de Commits e Versionamento:**
   - **Incremento Contínuo:** A cada novo commit que trouxer melhorias, correções ou novas funcionalidades, a versão deve ser incrementada (ex: de `v1.0.0` para `v1.0.1` para correções ou pequenas alterações, ou `v1.1.0` para novos recursos).
   - **Mensagem do Commit:** A nova versão atualizada deve ser incluída como prefixo na mensagem do seu commit no Git. Exemplo:
     ```bash
     git commit -m "v1.0.1: Corrige bug na exibição do RG mascarado"
     ```

---

## 🔒 Segurança e LGPD

1.  **Senhas Fortes:** Criptografadas no banco usando `bcrypt` (Laravel Hash).
2.  **Proteção de Informações:** Uso de Gates (`view-cpf`, `edit-data`, `manage-users`) no backend para bloquear requisições não autorizadas.
3.  **Auditoria e Evolução:** Histórico de alterações e auditoria de ações registradas diretamente no perfil do acolhido.
4.  **Tratamento de Mídias Sigilosas:** Arquivos e evoluções técnicas classificados como sigilosos não são enviados no payload de resposta para operadores que não possuem o nível necessário de acesso.
5.  **Exclusão Segura:** Modal interativo de confirmação exige clique adicional para que a conta seja permanentemente eliminada pelo Diretor.
