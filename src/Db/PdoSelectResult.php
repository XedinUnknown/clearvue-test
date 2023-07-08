<?php

declare(strict_types=1);

namespace Clearvue\Test1\Db;

use Iterator;
use PDO;
use PDOStatement;
use UnexpectedValueException;

/**
 * A result of a `SELECT` PDO query.
 */
class PdoSelectResult implements SelectResultInterface, Iterator
{
    protected const FETCH_MODE = PDO::FETCH_ASSOC;

    protected PDOStatement $select;
    protected int $rowsFound;

    /** @var ?mixed */
    protected $currentData;
    protected $currentIndex = 0;

    /**
     * @param PDOStatement $select A prepared and executed `SELECT` statement.
     * @param int $rowsFound The total number of rows found.
     */
    public function __construct(PDOStatement $select, int $rowsFound)
    {
        $this->select = $select;
        $this->rowsFound = $rowsFound;
    }

    /**
     * @inheritDoc
     */
    public function getFoundRowsCount(): int
    {
        return $this->rowsFound;
    }

    /**
     * Retrieves the configured iterator for the statement.
     */
    protected function getIterator(): Iterator
    {
        if ($this->iterator === null) {
            $statement = $this->select;
            $statement->setFetchMode(PDO::FETCH_ASSOC);

            $this->iterator = $statement->getIterator();
        }

        return $this->iterator;
    }

    /**
     * @inheritDoc
     */
    public function current(): mixed
    {
        return $this->currentData;
    }

    /**
     * @inheritDoc
     */
    public function next(): void
    {
        $this->currentData = $this->select->fetch(static::FETCH_MODE, PDO::FETCH_ORI_NEXT);
        $this->currentIndex++;
    }

    /**
     * @inheritDoc
     */
    public function key(): mixed
    {
        return $this->currentIndex;
    }

    /**
     * @inheritDoc
     */
    public function valid(): bool
    {
        return $this->currentData !== false;
    }

    /**
     * @inheritDoc
     */
    public function rewind(): void
    {
        $data = $this->select->fetch(static::FETCH_MODE, PDO::FETCH_ORI_FIRST);
        if ($data === false) {
            throw new UnexpectedValueException('Could not rewind PDO select result');
        }

        $this->currentData = $data;
        $this->currentIndex = 0;
    }
}
