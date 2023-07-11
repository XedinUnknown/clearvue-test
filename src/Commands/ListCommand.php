<?php

declare(strict_types=1);

namespace Clearvue\Test1\Commands;

use Clearvue\Test1\Db\SelectResultInterface;
use Clearvue\Test1\Transform\TransformerInterface;
use PDO;

/**
 * Retrieves a list of models.
 *
 * @template-covariant DTO of array
 * @template-covariant Model of object
 */
class ListCommand
{
    /** @use ListCommandTrait<DTO, Model> */
    use ListCommandTrait;

    /**
     * @param PDO $db The database connection.
     * @param string $tableName The name of the table to list items from.
     * @param TransformerInterface<Model, DTO> $hydrator The transformer that hydrates item data into objects.
     */
    public function __construct(
        protected PDO $db,
        protected string $tableName,
        protected TransformerInterface $hydrator
    ) {
    }

    /**
     * Retrieves a list of models.
     *
     * @param int|null $perPage How many records per page to retrieve, or no limit if `null`.
     * @param int $page The index of the result page.
     * @return SelectResultInterface<Model> A select result with hydrated models.
     */
    public function listAll(?int $perPage = null, int $page = 0): SelectResultInterface
    {
        $records = $this->getAll($this->tableName, $perPage, $page);
        $objects = $this->hydrateSelected($records, $this->hydrator);

        return $objects;
    }

    /**
     * @inheritDoc
     */
    protected function getConnection(): PDO
    {
        return $this->db;
    }
}
