<?php

declare(strict_types=1);

namespace Clearvue\Test1\Db;

use Iterator;
use IteratorAggregate;
use Traversable;

/**
 * A select result based on a simple list.
 */
class IteratorSelectResult implements SelectResultInterface, IteratorAggregate
{
    public function __construct(
        protected Iterator $iterator,
        protected int $foundRowsCount,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getFoundRowsCount(): int
    {
        return $this->foundRowsCount;
    }

    /**
     * @inheritDoc
     */
    public function getIterator(): Traversable
    {
        return $this->iterator;
    }
}
