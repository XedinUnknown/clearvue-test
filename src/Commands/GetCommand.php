<?php

declare(strict_types=1);

namespace Clearvue\Test1\Commands;

use Clearvue\Test1\Db\SelectResultInterface;
use Clearvue\Test1\Transform\TransformerInterface;
use PDO;
use RuntimeException;

/**
 * Retrieves a single DTOs.
 *
 * @template-covariant DTO of array
 * @template-covariant Model of object
 */
class GetCommand
{
    /** @use GetCommandTrait<DTO, Model> */
    use GetCommandTrait;

    /**
     * @param PDO $db The database connection.
     * @param string $tableName The name of the table to list items from.
     * @param TransformerInterface<Model, DTO> $hydrator The transformer that hydrates item data into objects.
     */
    public function __construct(
        protected PDO $db,
        protected string $tableName,
        protected string $primaryKeyFieldName,
        protected TransformerInterface $hydrator
    ) {
    }

    /**
     * Retrieves a single model.
     *
     * @param int $primaryKey The value of the primary key field to retrieve the record by.
     * @return Model The model.
     */
    public function get(int $primaryKey): object
    {
        $record = $this->getOne($this->tableName, $primaryKey);
        $model = $this->hydrateModel($record, $this->hydrator);

        return $model;
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
    protected function getPrimaryKeyFieldName(): string
    {
        return $this->primaryKeyFieldName;
    }
}
