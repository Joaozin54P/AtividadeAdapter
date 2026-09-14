# Padrão de Modelagem de Projetos — Adapter

Atividade em dupla, desenvolvida por **João Pedro Machado** e **Giovanna Aparecida**,
com base no projeto oficial [DesignPatternsPHP](https://github.com/DesignPatternsPHP/DesignPatternsPHP),
implementando o padrão estrutural **Adapter** no domínio `Structural/Adapter`.

## Objetivo

Permitir que um código cliente, feito para consumir a interface `Book`,
consiga interagir com um leitor digital `Kindle`, cuja interface (`EBook`)
possui métodos com nomes e tipos de retorno incompatíveis — sem alterar
nem o cliente, nem a classe `Kindle`.

## Autores

- João Pedro Machado
- Giovanna Aparecida

## Passos executados

### 1. Clonagem do repositório base

```bash
git clone https://github.com/DesignPatternsPHP/DesignPatternsPHP.git
cd DesignPatternsPHP
```

### 2. Instalação de dependências

```bash
composer install
```

### 3. Mapeamento do domínio (`Structural/Adapter/`)

| Arquivo | Papel no padrão | Descrição |
|---|---|---|
| `Book.php` | **Target** (interface) | Contrato esperado pelo cliente: `open()`, `turnPage()`, `getPage(): int` |
| `PaperBook.php` | Implementação concreta do Target | Atende diretamente à interface `Book` |
| `EBook.php` | **Adaptee** (interface) | Subsistema externo, com `unlock()`, `pressNext()`, `getPage(): int[]` |
| `Kindle.php` | Implementação concreta do Adaptee | Simula um leitor digital de terceiros |
| `EBookAdapter.php` | **Adapter** | Classe criada nesta atividade |

### 4. Identificação do conflito

O cliente só sabe conversar com a interface `Book`. A classe `Kindle`
implementa `EBook`, cujos métodos têm nomes diferentes (`unlock` em vez
de `open`, `pressNext` em vez de `turnPage`) e `getPage()` retorna um
`array` em vez de um `int`. Por isso, uma instância de `Kindle` não pode
ser entregue diretamente a um cliente que espera um `Book`.

### 5. Criação da classe adaptadora (`EBookAdapter.php`)

Arquivo criado em `Structural/Adapter/EBookAdapter.php`.

A classe:
- **Implementa a interface `Book`**, garantindo compatibilidade com o cliente;
- **Recebe uma instância de `EBook` via injeção de dependência** no construtor, guardada por **composição** (não herança);
- **Traduz cada chamada**:
  - `open()` → delega para `unlock()`
  - `turnPage()` → delega para `pressNext()`
  - `getPage(): int` → chama `getPage(): array` do `EBook` e retorna apenas a posição `[0]`, resolvendo a incompatibilidade de tipos.

```php
<?php

declare(strict_types=1);

namespace DesignPatterns\Structural\Adapter;

class EBookAdapter implements Book
{
    private EBook $eBook;

    public function __construct(EBook $eBook)
    {
        $this->eBook = $eBook;
    }

    public function open(): void
    {
        $this->eBook->unlock();
    }

    public function turnPage(): void
    {
        $this->eBook->pressNext();
    }

    public function getPage(): int
    {
        return $this->eBook->getPage()[0];
    }
}
```

### 6. Uso / exemplo de consumo pelo cliente

```php
use DesignPatterns\Structural\Adapter\Kindle;
use DesignPatterns\Structural\Adapter\EBookAdapter;

$kindle = new Kindle();
$book = new EBookAdapter($kindle);

$book->open();      // chama unlock() internamente
$book->turnPage();   // chama pressNext() internamente
echo $book->getPage(); // retorna int, mesmo o Kindle devolvendo array
```

### 7. Testes

O próprio repositório traz testes automatizados que validam o cenário:

```bash
php -d error_reporting=E_ALL^E_DEPRECATED vendor/bin/phpunit Structural/Adapter/Tests/AdapterTest.php
```

### 8. Versionamento

```bash
git init
git add .
git commit -m "feat: implementa EBookAdapter (padrão Adapter) e adiciona README"
git remote add origin https://github.com/Joaozin54P/AtividadeAdapter.git
git branch -M main
git push -u origin main
```

## Diagrama (resumo)

```
Cliente -> Book <- EBookAdapter -> EBook <- Kindle
```

O cliente não conhece o `Kindle` nem seus métodos; ele enxerga apenas um
`Book` comum. O `EBookAdapter` é quem sabe traduzir as chamadas.

## Princípios aplicados

- **Composição sobre herança**: o adapter guarda uma referência a `EBook`, em vez de herdar de `Kindle`.
- **Injeção de dependência**: a instância de `EBook` é recebida pelo construtor, facilitando testes e substituições.
- **Open/Closed Principle**: cliente e `Kindle` permanecem intocados; toda a tradução fica isolada no `EBookAdapter`.
- **Single Responsibility Principle**: o `EBookAdapter` tem uma única responsabilidade — traduzir chamadas entre as duas interfaces.
