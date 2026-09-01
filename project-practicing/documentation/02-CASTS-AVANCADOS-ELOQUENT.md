# 🔮 Guia de Estudo: Casts Avançados no Eloquent (Laravel Moderno)

## 1. O Que são Casts e Por Que Eles São Essenciais?

No banco de dados, tudo é armazenado em tipos primitivos (`VARCHAR`, `INT`, `TEXT`, `JSON`, `DATETIME`).
No PHP orientado a objetos, queremos trabalhar com **Objetos Ricos**, **Datas Imutáveis**, **Enums**, **Coleções** e **Tipos Seguros**.

O **Eloquent Cast** é a ponte automática que converte os dados entre o Banco de Dados (primitivo) e a sua Aplicação PHP (objeto/tipo).

---

## 2. A Nova Forma: O Método `casts()` no Laravel 11+

Historicamente, usava-se uma propriedade `protected $casts = [...]`. No Laravel moderno, utiliza-se o método protegido `casts()`:

### 🌟 Por que o método `casts()` é superior?
1. **Permite chamadas estáticas e métodos:** Você pode usar `AsArrayObject::class` ou chamadas com parâmetros dinâmicos.
2. **Não é sobrescrito facilmente por herança acidental.**
3. **Melhor inferência estática de tipos (PHPStan / Larastan).**

```php
namespace App\Models;

use App\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected function casts(): array
    {
        return [
            'is_completed' => 'boolean',
            'deadline_at' => 'immutable_datetime', // Cria instância de CarbonImmutable
            'status' => TaskStatus::class,
            'metadata' => AsArrayObject::class,    // JSON tratado como ArrayObject
            'tags' => AsCollection::class,         // JSON tratado como Laravel Collection
            'api_secret' => 'encrypted',           // Criptografado no banco automaticamente
        ];
    }
}
```

---

## 3. Casts JSON Modernos: `AsArrayObject` vs `AsCollection`

### ❌ O problema do cast `'array'` antigo:
Antigamente, se você fizesse `'metadata' => 'array'`, ao alterar uma chave interna:
```php
// Com o cast antigo 'array', isso NÃO marcava a model como suja (dirty)!
$task->metadata['theme'] = 'dark';
$task->save(); // ⚠️ Em versões antigas muitas vezes não salvava porque $task->isDirty('metadata') dava false!
```

### ✅ A Solução Moderna: `AsArrayObject::class`
Permite manipular propriedades JSON aninhadas diretamente como se fossem arrays ou objetos normais do PHP, com detecção automática de alterações:

```php
// No Model:
'settings' => AsArrayObject::class,

// No código:
$user->settings['notifications']['email'] = true;
$user->settings['theme'] = 'dark';
$user->save(); // ✨ O Laravel detecta a alteração interna e gera o JSON correto no UPDATE!
```

### ✅ `AsCollection::class`
Converte uma coluna JSON em uma **Laravel Collection** nativa:

```php
// No Model:
'tags' => AsCollection::class,

// No código:
$task->tags->push('laravel');
$task->tags = $task->tags->unique();

// Métodos de Collection funcionam diretamente:
if ($task->tags->contains('urgente')) {
    // ...
}
```

---

## 4. Casts de Criptografia: `encrypted`

Se você precisa salvar dados confidenciais (ex: chaves de API, tokens externos, CPF/Documentos sensíveis) e não quer que fiquem em texto puro no banco:

```php
protected function casts(): array
{
    return [
        'api_token' => 'encrypted',
        'sensitive_config' => 'encrypted:array', // Criptografa array em JSON
    ];
}
```

- Ao salvar: o Laravel criptografa automaticamente com a `APP_KEY`.
- Ao ler: o Laravel descriptografa de forma transparente.
- No banco de dados fica uma string cifrada (AES-256).

---

## 5. Criando seu Próprio Custom Cast (`CastsAttributes`)

Quando você tem um tipo complexo que o Laravel não conhece (ex: um Value Object de `Money`, `CPF`, `Coordinates` ou `Address`), você pode criar um Custom Cast.

### Exemplo: Cast para `Money` (Valor em Centavos no Banco vs Objeto em Reais no PHP)

Gere com o artisan:
```bash
php artisan make:cast MoneyCast
```

Arquivo `app/Casts/MoneyCast.php`:
```php
namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class MoneyCast implements CastsAttributes
{
    /**
     * Transforma o valor do banco (centavos inteiros) para reais ao acessar a Model.
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): float
    {
        return $value !== null ? ((int) $value) / 100 : 0.00;
    }

    /**
     * Transforma o valor de reais para centavos inteiros antes de salvar no banco.
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): int
    {
        return $value !== null ? (int) round(((float) $value) * 100) : 0;
    }
}
```

### Usando no Model:
```php
protected function casts(): array
{
    return [
        'price' => MoneyCast::class,
    ];
}

// No código:
$product->price = 19.90; 
$product->save(); // Salva 1990 no banco (evita problemas de ponto flutuante em MySQL/Postgres!)

echo $product->price; // Retorna float: 19.90
```

---

## 6. Resumo das Escolhas de Casts

| Tipo de Dado | Cast Recomendado | O que acontece |
| :--- | :--- | :--- |
| **Enum do PHP** | `StatusEnum::class` | Converte string/int do banco para a instância do Enum. |
| **JSON Aninhado** | `AsArrayObject::class` | Permite mutações em chaves `$model->json['key'] = 'val'` com salvamento seguro. |
| **Lista de Itens JSON** | `AsCollection::class` | Transforma o JSON em uma `Illuminate\Support\Collection`. |
| **Dado Sensível** | `'encrypted'` | Criptografa no banco e descriptografa na leitura. |
| **Data/Hora** | `'immutable_datetime'` | Cria `CarbonImmutable` prevenindo mutações colaterais em datas. |
| **Objeto de Domínio** | `CustomCast::class` | Regra customizada via `CastsAttributes`. |
