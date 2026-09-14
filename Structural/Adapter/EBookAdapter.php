<?php

declare(strict_types=1);

namespace DesignPatterns\Structural\Adapter;

/**
 * EBookAdapter
 *
 * Implementa a interface Book (Target) para manter compatibilidade com o
 * código cliente, enquanto internamente traduz cada chamada para os
 * métodos equivalentes de um objeto EBook (Adaptee), recebido via
 * composição no construtor (injeção de dependência).
 *
 * Isso permite que um leitor digital como o Kindle, cuja interface possui
 * nomenclatura e tipos de retorno diferentes, seja utilizado em qualquer
 * lugar onde um Book seja esperado, sem alterar o cliente nem a classe
 * Kindle (Open/Closed Principle).
 */
class EBookAdapter implements Book
{
    /**
     * @var EBook Instância do sistema externo (Adaptee) que será adaptada
     */
    private EBook $eBook;

    /**
     * Recebe a instância de EBook via injeção de dependência.
     * A composição é usada no lugar de herança para manter baixo
     * acoplamento e permitir trocar a implementação em tempo de execução.
     */
    public function __construct(EBook $eBook)
    {
        $this->eBook = $eBook;
    }

    /**
     * Traduz a chamada open() do cliente para unlock() do EBook.
     */
    public function open(): void
    {
        $this->eBook->unlock();
    }

    /**
     * Traduz a chamada turnPage() do cliente para pressNext() do EBook.
     */
    public function turnPage(): void
    {
        $this->eBook->pressNext();
    }

    /**
     * O EBook retorna um array de páginas (int[]); o contrato Book espera
     * um único int. Aqui pegamos a posição [0] do array para resolver a
     * incompatibilidade de tipos de retorno.
     */
    public function getPage(): int
    {
        return $this->eBook->getPage()[0];
    }
}
