# 🧪 Guia Completo e Definitivo de Testes: Pest PHP & Laravel

> **Propósito deste documento:** Um guia de consulta rápida de todas as asserções, expectativas e ferramentas de teste disponíveis no Pest PHP e no ecossistema Laravel. 
> 
> * 🟢 **[FEIJÃO COM ARROZ]:** Métodos fundamentais usados em 90% dos testes do dia a dia.
> * 🟡 **[EXTRA / ESPECIALIZADO]:** Métodos avançados, matemáticos, de sistema, mocks, time-travel e arquitetura.

---

## 📑 Índice de Navegação

1. [Expectativas de Valor e Tipos Básicos](#1-expectativas-de-valor-e-tipos-básicos-expect)
2. [Checagem de Tipos Primitivos e Classes](#2-checagem-de-tipos-primitivos-e-classes)
3. [Comparações Numéricas e Matemáticas](#3-comparações-numéricas-e-matemáticas)
4. [Manipulação e Validação de Textos (Strings)](#4-manipulação-e-validação-de-textos-strings)
5. [Arrays e Coleções](#5-arrays-e-coleções)
6. [Sistema de Arquivos e Disco](#6-sistema-de-arquivos-e-disco)
7. [Exceções e Lançamento de Erros](#7-exceções-e-lançamento-de-erros)
8. [Modificadores e Encadeamentos do Pest](#8-modificadores-e-encadeamentos-do-pest)
9. [Laravel: Banco de Dados (Database)](#9-laravel-banco-de-dados-database)
10. [Laravel: Requisições HTTP e Páginas](#10-laravel-requisições-http-e-páginas)
11. [Laravel: Autenticação e Sessão](#11-laravel-autenticação-e-sessão)
12. [Laravel: Simulação com Fakes (Mails, Queues, Events)](#12-laravel-simulação-com-fakes-mails-queues-events)
13. [Manipulação do Tempo (Time Travel)](#13-manipulação-do-tempo-time-travel)
14. [Testes de Arquitetura com Pest (arch)](#14-testes-de-arquitetura-com-pest-arch)
15. [Livewire: Testes de Telas e Componentes Reativos](#15-livewire-testes-de-telas-e-componentes-reativos)

---

## 1. Expectativas de Valor e Tipos Básicos (`expect()`)

### 🟢 [FEIJÃO COM ARROZ]
| Asserção | O que valida? | Exemplo de Código |
| :--- | :--- | :--- |
| `->toBe($x)` | Identidade estrita (`===`). Mesmo valor e mesmo tipo. | `expect($user->role)->toBe(UserRole::Admin);` |
| `->toEqual($x)` | Equivalência de valor (`==`). Compara arrays/objetos sem exigir a mesma referência de memória. | `expect($task->metadata)->toEqual(['ip' => '127.0.0.1']);` |
| `->toBeTrue()` | Garante que o valor é estritamente `true`. | `expect($user->isAdministrador())->toBeTrue();` |
| `->toBeFalse()` | Garante que o valor é estritamente `false`. | `expect($task->isFinal())->toBeFalse();` |
| `->toBeNull()` | Garante que o valor é `null`. | `expect($task->deadline_at)->toBeNull();` |
| `->toBeEmpty()` | Garante que a string, array ou collection está vazia. | `expect($project->tasks)->toBeEmpty();` |

### 🟡 [EXTRA / ESPECIALIZADO]
| Asserção | O que valida? | Exemplo de Código |
| :--- | :--- | :--- |
| `->toBeTruthy()` | Se é avaliado como verdadeiro segundo coerção do PHP (`1`, `"texto"`, `[1]`). | `expect($config)->toBeTruthy();` |
| `->toBeFalsy()` | Se é avaliado como falso segundo coerção do PHP (`0`, `""`, `[]`, `null`). | `expect($tentativas)->toBeFalsy();` |
| `->toEqualCanonicalizing($x)` | Se dois arrays contêm os mesmos elementos, ignorando a ordem de inserção. | `expect([3, 1, 2])->toEqualCanonicalizing([1, 2, 3]);` |
| `->toEqualWithDelta($x, $delta)` | Se dois números de ponto flutuante (`float`) são iguais dentro de uma margem de tolerância. | `expect($precoFinal)->toEqualWithDelta(10.50, 0.01);` |
| `->toBeNan()` | Se o resultado da operação matemática é "Not a Number" (`NaN`). | `expect(acos(5))->toBeNan();` |
| `->toBeInfinite()` | Se o resultado numérico é infinito (`INF`). | `expect(log(0))->toBeInfinite();` |

---

## 2. Checagem de Tipos Primitivos e Classes

### 🟢 [FEIJÃO COM ARROZ]
| Asserção | O que valida? | Exemplo de Código |
| :--- | :--- | :--- |
| `->toBeInstanceOf($class)` | Se o objeto foi instanciado a partir daquela classe ou implementa a interface. | `expect($task->deadline_at)->toBeInstanceOf(Carbon::class);` |
| `->toBeString()` | Se o dado é uma `string` nativa do PHP. | `expect($user->name)->toBeString();` |
| `->toBeInt()` | Se o dado é um número inteiro (`int`). | `expect($user->id)->toBeInt();` |
| `->toBeArray()` | Se o dado é um `array` nativo do PHP. | `expect(UserRole::values())->toBeArray();` |

### 🟡 [EXTRA / ESPECIALIZADO]
| Asserção | O que valida? | Exemplo de Código |
| :--- | :--- | :--- |
| `->toBeFloat()` | Se o número tem casas decimais (`float` / `double`). | `expect($taxa)->toBeFloat();` |
| `->toBeBool()` | Se o dado é um booleano nativo (`bool`). | `expect($ativo)->toBeBool();` |
| `->toBeObject()` | Se o dado é uma instância de qualquer objeto genérico. | `expect($dto)->toBeObject();` |
| `->toBeJson()` | Se a string é um JSON estruturalmente válido. | `expect($jsonString)->toBeJson();` |
| `->toBeNumeric()` | Se é um número ou uma string contendo representação numérica (`"120"`). | `expect($parametro)->toBeNumeric();` |
| `->toBeIterable()` | Se o dado pode ser percorrido por um loop `foreach`. | `expect($lista)->toBeIterable();` |
| `->toBeCallable()` | Se é uma função anônima ou método chamável. | `expect($callback)->toBeCallable();` |
| `->toBeResource()` | Se é um ponteiro de recurso de baixo nível do PHP (ex: `fopen()`). | `expect($stream)->toBeResource();` |

---

## 3. Comparações Numéricas e Matemáticas

### 🟢 [FEIJÃO COM ARROZ]
| Asserção | O que valida? | Exemplo de Código |
| :--- | :--- | :--- |
| `->toBeGreaterThan($n)` | Maior estrito (`>`). | `expect($user->age)->toBeGreaterThan(18);` |
| `->toBeGreaterThanOrEqual($n)`| Maior ou igual (`>=`). | `expect($task->priority_level)->toBeGreaterThanOrEqual(1);` |
| `->toBeLessThan($n)` | Menor estrito (`<`). | `expect($estoque)->toBeLessThan(5);` |
| `->toBeLessThanOrEqual($n)` | Menor ou igual (`<=`). | `expect($desconto)->toBeLessThanOrEqual(100);` |

### 🟡 [EXTRA / ESPECIALIZADO]
| Asserção | O que valida? | Exemplo de Código |
| :--- | :--- | :--- |
| `->toBeBetween($min, $max)` | Se o valor numérico está dentro da faixa inclusiva `[min, max]`. | `expect($nota)->toBeBetween(0, 10);` |

---

## 4. Manipulação e Validação de Textos (Strings)

### 🟢 [FEIJÃO COM ARROZ]
| Asserção | O que valida? | Exemplo de Código |
| :--- | :--- | :--- |
| `->toContain($trecho)` | Se a string contém determinado trecho de texto. | `expect($email)->toContain('@gmail.com');` |
| `->toStartWith($prefixo)` | Se a string inicia com o prefixo especificado. | `expect($apiKey)->toStartWith('sk_live_');` |
| `->toEndWith($sufixo)` | Se a string finaliza com o sufixo especificado. | `expect($nomeArquivo)->toEndWith('.pdf');` |

### 🟡 [EXTRA / ESPECIALIZADO]
| Asserção | O que valida? | Exemplo de Código |
| :--- | :--- | :--- |
| `->toHaveLength($qtd)` | Quantidade exata de caracteres na string. | `expect($cpfLimpo)->toHaveLength(11);` |
| `->toMatch($regex)` | Se o texto satisfaz um padrão de Expressão Regular (Regex). | `expect($telefone)->toMatch('/^\(\d{2}\)\s\d{5}-\d{4}$/');` |
| `->toMatchSnapshot()` | Compara o texto com um arquivo snapshot de referência salvo em disco. | `expect($saidaXml)->toMatchSnapshot();` |

---

## 5. Arrays e Coleções

### 🟢 [FEIJÃO COM ARROZ]
| Asserção | O que valida? | Exemplo de Código |
| :--- | :--- | :--- |
| `->toHaveCount($n)` | Quantidade exata de itens contidos no array ou collection. | `expect($project->tasks)->toHaveCount(5);` |
| `->toContain($item)` | Se determinado elemento está contido no array. | `expect(UserRole::values())->toContain('administrador');` |
| `->toHaveKey($chave)` | Se o array possui a chave definida. | `expect($config)->toHaveKey('database');` |
| `->toBeIn($lista)` | O inverso de toContain: se a variável está contida dentro do array. | `expect('administrador')->toBeIn(UserRole::values());` |

### 🟡 [EXTRA / ESPECIALIZADO]
| Asserção | O que valida? | Exemplo de Código |
| :--- | :--- | :--- |
| `->toHaveKeys(['a', 'b'])` | Se o array possui todas as chaves especificadas de uma só vez. | `expect($metadados)->toHaveKeys(['ip', 'browser', 'os']);` |
| `->toMatchArray($subconjunto)`| Verifica se o array contém pelo menos aquele subconjunto de pares chave-valor. | `expect($resposta)->toMatchArray(['status' => 'sucesso']);` |
| `->toContainEqual($objeto)` | Checa se um objeto com propriedades idênticas existe na coleção. | `expect($usuarios)->toContainEqual($usuarioEsperado);` |

---

## 6. Sistema de Arquivos e Disco

### 🟡 [EXTRA / ESPECIALIZADO]
| Asserção | O que valida? | Exemplo de Código |
| :--- | :--- | :--- |
| `->toBeFile()` | Se o caminho é um arquivo existente no sistema de arquivos. | `expect(storage_path('exports/relatorio.csv'))->toBeFile();` |
| `->toBeDirectory()` | Se o caminho é um diretório existente. | `expect(app_path('Enums'))->toBeDirectory();` |
| `->toBeReadable()` | Se o arquivo possui permissão de leitura pelo processo PHP. | `expect($arquivoConfig)->toBeReadable();` |
| `->toBeWritable()` | Se o diretório/arquivo possui permissão de escrita pelo PHP. | `expect(storage_path('logs'))->toBeWritable();` |

---

## 7. Exceções e Lançamento de Erros

### 🟢 [FEIJÃO COM ARROZ]
| Asserção | O que valida? | Exemplo de Código |
| :--- | :--- | :--- |
| `->toThrow($classeErro)` | Garante que o bloco de código disparou a exceção esperada. | `expect(fn() => $user->delete())->toThrow(DomainException::class);` |
| `->toThrow($mensagem)` | Garante que a exceção disparada contém a mensagem exata de erro. | `expect(fn() => $conta->sacar(500))->toThrow('Saldo insuficiente');` |

---

## 8. Modificadores e Encadeamentos do Pest

### 🟢 [FEIJÃO COM ARROZ]
| Modificador | O que faz? | Exemplo de Código |
| :--- | :--- | :--- |
| `->and(...)` | Continua a execução encadeando uma nova expectativa na mesma instrução. | `expect($nome)->toBe('Dereck')->and($idade)->toBe(25);` |
| `->not->` | **Inverte a lógica** de qualquer asserção do Pest. | `expect($user->role)->not->toBe(UserRole::Cliente);`<br>`expect($task->deadline_at)->not->toBeNull();` |

### 🟡 [EXTRA / ESPECIALIZADO]
| Modificador | O que faz? | Exemplo de Código |
| :--- | :--- | :--- |
| `->each->` | Aplica a asserção automaticamente para **todos os elementos** do array. | `expect($tasks)->each->toBeInstanceOf(Task::class);` |
| `->sequence(...)` | Valida uma lista ordenada de itens onde cada um atende a uma expectativa sequencial. | `expect($numeros)->sequence(fn($e) => $e->toBe(1), fn($e) => $e->toBe(2));` |
| `->dd()` / `->ray()` | Interrompe o teste e exibe o dump da variável inspecionada. | `expect($usuario)->dd();` |
| `with([...])` | **Datasets:** Executa o mesmo caso de teste várias vezes alimentado por dados de entrada. | `test('status válidos', function ($st) { ... })->with(['aberto', 'fechado']);` |

---

## 9. Laravel: Banco de Dados (Database)
*(Namespace: `use function Pest\Laravel\{nomeDaFuncao};`)*

### 🟢 [FEIJÃO COM ARROZ]
| Função | O que valida? | Exemplo de Código |
| :--- | :--- | :--- |
| `assertDatabaseHas($tabela, $dados)` | Verifica se existe registro no banco com os dados informados. | `assertDatabaseHas('users', ['email' => 'dereck@test.com']);` |
| `assertDatabaseMissing($tabela, $dados)` | Verifica se **não existe** nenhum registro no banco com esses dados. | `assertDatabaseMissing('users', ['email' => 'removido@test.com']);` |
| `assertDatabaseCount($tabela, $qtd)` | Confere a quantidade exata de linhas gravadas na tabela. | `assertDatabaseCount('tasks', 10);` |
| `assertDatabaseEmpty($tabela)` | Confere se a tabela está 100% vazia (zero linhas). | `assertDatabaseEmpty('projects');` |
| `assertModelExists($model)` | Confere se a instância da Model persiste no banco. | `assertModelExists($user);` |
| `assertModelMissing($model)` | Confere se a Model foi removida do banco de dados. | `assertModelMissing($task);` |

### 🟡 [EXTRA / ESPECIALIZADO]
| Função | O que valida? | Exemplo de Código |
| :--- | :--- | :--- |
| `assertSoftDeleted($tabela, $dados)` | Se o registro foi apagado via Soft Delete (`deleted_at != null`). | `assertSoftDeleted('tasks', ['id' => 1]);` |
| `assertNotSoftDeleted($tabela, $dados)`| Se o registro continua ativo (não sofreu soft delete). | `assertNotSoftDeleted('tasks', ['id' => 1]);` |
| `assertDatabaseHasTable($tabela)` | Se a tabela existe fisicamente no banco de dados. | `assertDatabaseHasTable('users');` |

---

## 10. Laravel: Requisições HTTP e Páginas
*(Namespace: `use function Pest\Laravel\{get, post, put, delete, actingAs};`)*

### 🟢 [FEIJÃO COM ARROZ]
| Comando / Asserção | O que valida? | Exemplo de Código |
| :--- | :--- | :--- |
| `actingAs($user)` | Autentica um usuário na sessão durante o teste. | `actingAs($admin)->get('/dashboard');` |
| `get($url)` / `post($url, $dados)` | Dispara uma requisição HTTP na aplicação. | `$res = post('/tasks', ['title' => 'Nova Tarefa']);` |
| `$res->assertOk()` | Resposta com status HTTP 200 (Sucesso). | `$res->assertOk();` |
| `$res->assertCreated()` | Resposta com status HTTP 201 (Criado com Sucesso). | `$res->assertCreated();` |
| `$res->assertRedirect($url)` | Garante que houve redirecionamento para o destino esperado. | `$res->assertRedirect('/login');` |
| `$res->assertForbidden()` | Resposta com status HTTP 403 (Bloqueado por Policy ou Gate). | `$res->assertForbidden();` |
| `$res->assertUnauthorized()` | Resposta com status HTTP 401 (Visitante não autenticado). | `$res->assertUnauthorized();` |
| `$res->assertNotFound()` | Resposta com status HTTP 404 (Rota ou recurso inexistente). | `$res->assertNotFound();` |
| `$res->assertSessionHasErrors(['campo'])`| Validação do Form Request reprovou o campo com erro. | `$res->assertSessionHasErrors(['title']);` |
| `$res->assertSessionHasNoErrors()` | Requisição processada sem nenhum erro de validação. | `$res->assertSessionHasNoErrors();` |

### 🟡 [EXTRA / ESPECIALIZADO]
| Comando / Asserção | O que valida? | Exemplo de Código |
| :--- | :--- | :--- |
| `$res->assertSee('Texto')` | Se o texto aparece no HTML renderizado da tela. | `$res->assertSee('Painel de Controle');` |
| `$res->assertDontSee('Texto')` | Se o texto não aparece na tela (ex: botão restrito a admin). | `$res->assertDontSee('Excluir Projeto');` |
| `$res->assertJson(['status' => 'ok'])`| Se a resposta da API contém o JSON especificado. | `$res->assertJson(['success' => true]);` |
| `$res->assertJsonPath('data.0.id', 1)`| Valida um campo específico dentro de um JSON aninhado via dot-notation. | `$res->assertJsonPath('user.email', 'dereck@test.com');` |
| `$res->assertJsonStructure([...])` | Valida se o formato das chaves retornadas pela API atende à estrutura. | `$res->assertJsonStructure(['data' => ['*' => ['id', 'title']]]);` |
| `$res->assertDownload('relatorio.pdf')` | Se a rota iniciou o download de um arquivo com aquele nome. | `$res->assertDownload('relatorio.pdf');` |

---

## 11. Laravel: Autenticação e Sessão

### 🟢 [FEIJÃO COM ARROZ]
| Função | O que valida? | Exemplo de Código |
| :--- | :--- | :--- |
| `assertAuthenticated()` | Garante que existe um usuário autenticado na sessão. | `assertAuthenticated();` |
| `assertGuest()` | Garante que o visitante atual está deslogado. | `assertGuest();` |
| `assertAuthenticatedAs($user)` | Garante que o usuário logado é exatamente a instância informada. | `assertAuthenticatedAs($admin);` |

### 🟡 [EXTRA / ESPECIALIZADO]
| Função | O que valida? | Exemplo de Código |
| :--- | :--- | :--- |
| `assertSessionHas('chave', 'valor')`| Se uma chave e valor específicos foram armazenados na sessão. | `assertSessionHas('status', 'Projeto salvo!');` |
| `assertSessionMissing('chave')` | Se a chave especificada não existe na sessão. | `assertSessionMissing('carrinho');` |

---

## 12. Laravel: Simulação com Fakes (Mails, Queues, Events)

### 🟡 [EXTRA / ESPECIALIZADO]
| Ferramenta Fake | O que simula? | Exemplo de Código |
| :--- | :--- | :--- |
| `Mail::fake()` | Impede envio real e intercepta e-mails para validação. | `Mail::fake();`<br>`Mail::assertSent(WelcomeMail::class);` |
| `Queue::fake()` | Impede execução assíncrona real e intercepta Jobs na fila. | `Queue::fake();`<br>`Queue::assertPushed(ExportReportJob::class);` |
| `Event::fake()` | Intercepta o disparo de eventos internos da aplicação. | `Event::fake();`<br>`Event::assertDispatched(TaskAssigned::class);` |
| `Notification::fake()` | Intercepta notificações disparadas para canais (SMS, Slack, etc). | `Notification::fake();`<br>`Notification::assertSentTo($user, TaskReminder::class);` |
| `Storage::fake('disco')` | Cria um disco de armazenamento virtual isolado em memória. | `Storage::fake('avatars');`<br>`Storage::disk('avatars')->assertExists('foto.jpg');` |

---

## 13. Manipulação do Tempo (Time Travel)

### 🟡 [EXTRA / ESPECIALIZADO]
| Função | O que faz? | Exemplo de Código |
| :--- | :--- | :--- |
| `travel($n)->days()` | Avança o relógio do PHP em $n dias. | `travel(5)->days();` |
| `travel($n)->hours()` | Avança o relógio em $n horas ou minutos. | `travel(30)->minutes();` |
| `travelTo($data)` | Teletransporta o relógio do PHP para uma data fixa. | `travelTo(Carbon::parse('2030-01-01'));` |
| `travelBack()` | Restaura o relógio do PHP para o horário real atual. | `travelBack();` |

---

## 14. Testes de Arquitetura com Pest (`arch()`)

### 🟡 [EXTRA / ESPECIALIZADO]
| Teste de Arquitetura | O que garante no projeto inteiro? | Exemplo de Código |
| :--- | :--- | :--- |
| `not->toBeUsed()` | Garante que ninguém deixou comandos de debug no código de produção. | `arch()->expect(['dd', 'dump', 'ray'])->not->toBeUsed();` |
| `toBeEnums()` | Garante que todas as classes da pasta `app/Enums` sejam Enums nativos. | `arch()->expect('App\Enums')->toBeEnums();` |
| `toExtend(...)` | Garante que todas as classes de Models herdem da classe Model do Eloquent. | `arch()->expect('App\Models')->toExtend(Model::class);` |
| `toHaveSuffix(...)` | Garante padrão de nomenclatura estrito para classes de uma pasta. | `arch()->expect('App\Http\Controllers')->toHaveSuffix('Controller');` |
| `toUseStrictTypes()` | Garante que todos os arquivos PHP do projeto declarem tipagem estrita. | `arch()->expect('App')->toUseStrictTypes();` |

---

## 15. Livewire: Testes de Telas e Componentes Reativos

### 🟡 [EXTRA / ESPECIALIZADO]
| Comando | O que faz? | Exemplo de Código |
| :--- | :--- | :--- |
| `Livewire::test(Component::class)` | Instancia o componente Livewire em ambiente de teste. | `Livewire::test(TaskBoard::class)` |
| `->set('propriedade', 'valor')` | Simula a digitação de um usuário em um input (`wire:model`). | `->set('title', 'Nova Tarefa')` |
| `->call('metodo')` | Simula o clique em um botão de ação (`wire:click`). | `->call('saveTask')` |
| `->assertSee('Texto')` | Checa se o texto foi renderizado no HTML reativo do componente. | `->assertSee('Tarefa criada com sucesso!')` |
| `->assertHasErrors(['campo'])` | Checa se as regras de validação do componente reprovaram o campo. | `->assertHasErrors(['title' => 'required'])` |
| `->assertDispatched('evento')` | Checa se o componente emitiu um evento para outro componente. | `->assertDispatched('task-created')` |
