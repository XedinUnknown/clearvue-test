<?php

declare(strict_types=1);

namespace Clearvue\Test1;

use Iterator;
use IteratorAggregate;
use OuterIterator;
use UnexpectedValueException;

/**
 * @template TKey
 * @template TValue
 * @psalm-type array = array
 * @implements Iterator<TKey, TValue>
 */
class CallbackIterator implements Iterator
{
    /** @var Iterator<TKey, array> */
    protected Iterator $innerIterator;
    /** @var ?TValue */
    protected $current = null;
    /** @var callable(array, TKey, static): TValue */
    protected $callback;

    /**
     * @param Iterator<TKey, array> $innerIterator
     * @param callable(array, TKey, static): TValue $callback
     */
    public function __construct(Iterator $innerIterator, callable $callback)
    {
        $this->innerIterator = $innerIterator;
        $this->callback = $callback;
    }

    /**
     * @inheritDoc
     *
     * @return TValue The current value.
     *
     * @throws UnexpectedValueException If problem getting current item from inner iterator.
     */
    public function current(): mixed
    {
        if ($this->current === null) {
            $innerCurrent = $this->getInnerIterator()->current();
            if ($innerCurrent === null) {
                throw new UnexpectedValueException('Inner iterator returned invalid current item');
            }

            $callback = $this->callback;
            $this->current = $callback($innerCurrent, $this->key(), $this);
        }

        return $this->current;
    }

    /**
     * @inheritDoc
     */
    public function next(): void
    {
        // Clear current cache
        if ($this->current !== null) {
            $this->current = null;
        }

        $this->getInnerIterator()->next();
    }

    /**
     * @inheritDoc
     *
     * @return TKey The current key.
     * @psalm-suppress MissingReturnType
     */
    public function key(): mixed
    {
        return $this->getInnerIterator()->key();
    }

    /**
     * @inheritDoc
     */
    public function valid(): bool
    {
        return $this->getInnerIterator()->valid();
    }

    /**
     * @inheritDoc
     */
    public function rewind(): void
    {
        $inner = $this->getInnerIterator();
        $inner->rewind();
    }

    /**
     * Retrieves the inner iterator.
     *
     * @return Iterator<TKey, array> The inner iterator.
     */
    protected function getInnerIterator(): Iterator
    {
        return $this->innerIterator;
    }
}
