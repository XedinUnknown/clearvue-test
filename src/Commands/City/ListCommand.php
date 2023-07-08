<?php

declare(strict_types=1);

namespace Clearvue\Test1\Commands\City;

use Clearvue\Test1\Commands\ListCommandTrait;
use Clearvue\Test1\Db\SelectResultInterface;
use Clearvue\Test1\Transform\TransformerInterface;
use PDO;

/**
 * Retrieves a list of DTOs.
 */
class ListCommand
{
    use ListCommandTrait;

    protected const MAX_ROWS = PHP_INT_MAX;

    protected PDO $db;
    protected string $tableName;
    /** @var TransformerInterface<object, array> */
    protected TransformerInterface $hydrator;

    /**
     * @param PDO $db The database connection.
     * @param string $tableName The name of the table to list items from.
     * @param TransformerInterface<object, array> $hydrator The transformer that hydrates item data into objects.
     */
    public function __construct(PDO $db, string $tableName, TransformerInterface $hydrator)
    {
        $this->db = $db;
        $this->tableName = $tableName;
        $this->hydrator = $hydrator;
    }

    /**
     * Retrieves a list of DTOs.
     *
     * @param int|null $perPage
     * @param int $page
     * @return SelectResultInterface
     */
    public function listAll(?int $perPage = null, int $page = 0): SelectResultInterface
    {
        $records = $this->getAll($this->tableName, $perPage, $page);
        $objects = $this->hydrateSelected($records);

        return $objects;
    }

    /**
     * @inheritDoc
     */
    protected function getConnection(): PDO
    {
        return $this->db;
    }

    /**
     * @inheritDoc
     */
    protected function getHydrator(): TransformerInterface
    {
        return $this->hydrator;
    }
}
