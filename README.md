# Padrão de Modelagem de Projetos — Adapter

Atividade em dupla, desenvolvida por **[Seu Nome]** e **Giovanna [Sobrenome]**,
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
git checkout -b feature/ebook-adapter
git add Structural/Adapter/EBookAdapter.php README.md
git commit -m "feat: implementa EBookAdapter (padrão Adapter) para compatibilizar Kindle com Book"
git push origin feature/ebook-adapter
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

---

# DesignPatternsPHP

[![Build Status](https://github.com/DesignPatternsPHP/DesignPatternsPHP/workflows/CI/badge.svg?branch=main)](https://github.com/DesignPatternsPHP/DesignPatternsPHP/actions)
[![Documentation Status](https://readthedocs.org/projects/designpatternsphp/badge/?version=latest)](https://designpatternsphp.readthedocs.io/?badge=latest)

[Read the Docs of DesignPatternsPHP](http://designpatternsphp.readthedocs.org)
or [Download as PDF/Epub](https://readthedocs.org/projects/designpatternsphp/downloads/)

This is a collection of known design patterns and some sample codes on how to implement them in PHP. Every pattern has a small list of examples.

I think the problem with patterns is that often people do know them but don't know when to apply which. Remember that each pattern has its own trade-offs. And you need to pay attention more to why you're choosing a certain pattern than to how to implement it.

## Installation
You should look at and run the tests to see what happens in the example.
To do this, you should install dependencies with `Composer` first:

```bash
$ composer install
```

Read more about how to install and use `Composer` on your local machine [here](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx).

To run the tests use `phpunit`:

```bash
$ ./vendor/bin/phpunit
```

## Using Docker (optional)

You can optionally build and browse the documentation using [Docker for Mac, Windows or Linux](https://docs.docker.com/compose/install/).

Just run:

```bash
$ docker-compose up --build
```

Go to [http://localhost:8080/](http://localhost:8080/) to read the generated documentation.

If you want to localize your documentation you can pass the locale as an argument to the docker build:

```bash
$ docker-compose build --build-arg language=de
$ docker-compose up
```

## Patterns

The patterns can be structured in roughly three different categories. Please click on the [:notebook:](http://en.wikipedia.org/wiki/Software_design_pattern) for a full explanation of the pattern on Wikipedia.

### [Creational](Creational)

* [AbstractFactory](Creational/AbstractFactory) [:notebook:](http://en.wikipedia.org/wiki/Abstract_factory_pattern)
* [Builder](Creational/Builder) [:notebook:](http://en.wikipedia.org/wiki/Builder_pattern)
* [FactoryMethod](Creational/FactoryMethod) [:notebook:](http://en.wikipedia.org/wiki/Factory_method_pattern)
* [Pool](Creational/Pool) [:notebook:](http://en.wikipedia.org/wiki/Object_pool_pattern)
* [Prototype](Creational/Prototype) [:notebook:](http://en.wikipedia.org/wiki/Prototype_pattern)
* [SimpleFactory](Creational/SimpleFactory)
* [Singleton](Creational/Singleton) [:notebook:](http://en.wikipedia.org/wiki/Singleton_pattern)
* [StaticFactory](Creational/StaticFactory)

### [Structural](Structural)

* [Adapter](Structural/Adapter) [:notebook:](http://en.wikipedia.org/wiki/Adapter_pattern)
* [Bridge](Structural/Bridge) [:notebook:](http://en.wikipedia.org/wiki/Bridge_pattern)
* [Composite](Structural/Composite) [:notebook:](http://en.wikipedia.org/wiki/Composite_pattern)
* [DataMapper](Structural/DataMapper) [:notebook:](http://en.wikipedia.org/wiki/Data_mapper_pattern)
* [Decorator](Structural/Decorator) [:notebook:](http://en.wikipedia.org/wiki/Decorator_pattern)
* [DependencyInjection](Structural/DependencyInjection) [:notebook:](http://en.wikipedia.org/wiki/Dependency_injection)
* [Facade](Structural/Facade) [:notebook:](http://en.wikipedia.org/wiki/Facade_pattern)
* [FluentInterface](Structural/FluentInterface) [:notebook:](http://en.wikipedia.org/wiki/Fluent_interface)
* [Flyweight](Structural/Flyweight) [:notebook:](https://en.wikipedia.org/wiki/Flyweight_pattern)
* [Proxy](Structural/Proxy) [:notebook:](http://en.wikipedia.org/wiki/Proxy_pattern)
* [Registry](Structural/Registry) [:notebook:](http://en.wikipedia.org/wiki/Service_locator_pattern)

### [Behavioral](Behavioral)

* [ChainOfResponsibilities](Behavioral/ChainOfResponsibilities) [:notebook:](http://en.wikipedia.org/wiki/Chain_of_responsibility_pattern)
* [Command](Behavioral/Command) [:notebook:](http://en.wikipedia.org/wiki/Command_pattern)
* [Interpreter](Behavioral/Interpreter) [:notebook:](https://en.wikipedia.org/wiki/Interpreter_pattern)  
* [Iterator](Behavioral/Iterator) [:notebook:](http://en.wikipedia.org/wiki/Iterator_pattern)
* [Mediator](Behavioral/Mediator) [:notebook:](http://en.wikipedia.org/wiki/Mediator_pattern)
* [Memento](Behavioral/Memento) [:notebook:](http://en.wikipedia.org/wiki/Memento_pattern)
* [NullObject](Behavioral/NullObject) [:notebook:](http://en.wikipedia.org/wiki/Null_Object_pattern)
* [Observer](Behavioral/Observer) [:notebook:](http://en.wikipedia.org/wiki/Observer_pattern)
* [Specification](Behavioral/Specification) [:notebook:](http://en.wikipedia.org/wiki/Specification_pattern)
* [State](Behavioral/State) [:notebook:](http://en.wikipedia.org/wiki/State_pattern)
* [Strategy](Behavioral/Strategy) [:notebook:](http://en.wikipedia.org/wiki/Strategy_pattern)
* [TemplateMethod](Behavioral/TemplateMethod) [:notebook:](http://en.wikipedia.org/wiki/Template_method_pattern)
* [Visitor](Behavioral/Visitor) [:notebook:](http://en.wikipedia.org/wiki/Visitor_pattern)

### [More](More)

* [EAV](More/EAV) [:notebook:](https://en.wikipedia.org/wiki/Entity%E2%80%93attribute%E2%80%93value_model)
* [Repository](More/Repository)
* [ServiceLocator](More/ServiceLocator) [:notebook:](http://en.wikipedia.org/wiki/Service_locator_pattern)


## Localization & Supported Languages
  The following languages are currently available. However not all are actively supported. 
  The current policy is: if there are no native-speaking maintainers we cannot support the given language. 
  We will still leave the translation as is but we will not update it as the base (English) changes.
  The table below list which language version is considered "supported" and who maintains it.

  We're sorry. We would love to maintain them but it is not possible for us. So if you would like to take care
  of one or more languages - contact us.

| Code  | Language  |    Documentation   |  Actively Supported?  | Maintainer |
| ------------------|-------------|------|-----------------------|------------|
| en    | English   | [Docs :notebook:](https://designpatternsphp.readthedocs.io/en/latest/) | YES | -- |
| de    | German    | [Docs :notebook:](https://designpatternsphp.readthedocs.io/de/latest/) | NO | - |
| ca    | Catalan   | [Docs :notebook:](https://designpatternsphp.readthedocs.io/ca/latest/) | NO | - |
| zh_CN | Chinese   | [Docs :notebook:](https://designpatternsphp.readthedocs.io/zh_CN/latest/) | NO | - |
| nl    | Dutch     | [Docs :notebook:](https://designpatternsphp.readthedocs.io/nl/latest/) | NO | - |
| bg    | Bulgarian | [Docs :notebook:](https://designpatternsphp.readthedocs.io/bg/latest/) | YES | -- |
| ja    | Japanese  | [Docs :notebook:](https://designpatternsphp.readthedocs.io/ja/latest/) | NO | - |
| pl    | Polish    | [Docs :notebook:](https://designpatternsphp.readthedocs.io/pl/latest/) | NO | - |
| pt_BR | Portuguese-Brazil | [Docs :notebook:](https://designpatternsphp.readthedocs.io/pt_BR/latest/) | NO | - |
| ru    | Russian   | [Docs :notebook:](https://designpatternsphp.readthedocs.io/ru/latest/) | NO | - |
| es    | Spanish   | [Docs :notebook:](https://designpatternsphp.readthedocs.io/es/latest/) | NO | - |
| es_MX | Spanish-Mexican | [Docs :notebook:](https://designpatternsphp.readthedocs.io/es_MX/latest/) | NO | - |
| tr    | Turkish   | [Docs :notebook:](https://designpatternsphp.readthedocs.io/tr/latest/) | NO | - |
| fr    | French | [Docs :notebook:](https://designpatternsphp.readthedocs.io/fr/latest/) | NO | - |
| it    | Italian | [Docs :notebook:](https://designpatternsphp.readthedocs.io/it/latest/) | NO | - |
| uk    | Ukrainian | [Docs :notebook:](https://designpatternsphp.readthedocs.io/uk/latest/)    | NO | - |
