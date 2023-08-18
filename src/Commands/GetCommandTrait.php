<?php

declare(strict_types=1);

namespace Clearvue\Test1\Commands;

use Clearvue\Test1\Transform\TransformerInterface;
use PDO;
use RuntimeException;
use UnexpectedValueException;

/**
 * Functionality for a simple get command.
 *
 * @template-covariant DTO of array
 * @template-covariant Model of object
 */
trait GetCommandTrait
{
    /**
     * Retrieves the data of one model by primary key.
     *
     * @param string $tableName The name of the table containing the model data.
     * @param int $primaryKey The primary key value.
     *
     * @phpcs:ignore SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification
     * @return DTO The model data.
     *
     * @throws RuntimeException If problem retrieving.
     */
    protected function getOne(string $tableName, int $primaryKey): array
    {
        $db = $this->getConnection();
        $primaryKeyFieldName = $this->getPrimaryKeyFieldName();
        $query = "SELECT * FROM `{$tableName}` WHERE `{$primaryKeyFieldName}` = :primaryKey";
        $statement = $db->prepare($query, [PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL]);
        $statement->bindValue(':primaryKey', $primaryKey, PDO::PARAM_INT);
        $statement->execute();
        $data = $statement->fetch(PDO::FETCH_ASSOC);

        if ($data === false) {
            throw new UnexpectedValueException(
                sprintf(
                    'Could not fetch record for "%1$s" #%2$d from "%3$s"',
                    $primaryKeyFieldName,
                    $primaryKey,
                    $tableName
                )
            );
        }

        return $data;
    }

    /**
     * Hydrates a single model based on provided data.
     *
     * @phpcs:ignore SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingTraversableTypeHintSpecification
     * @param DTO $data The data to use for hydration.
     * @param TransformerInterface<Model, DTO> $hydrator The transformer that will hydrate the model.
     *
     * @return Model The hydrated model.
     *
     * @throws RuntimeException If problem hydrating.
     */
    protected function hydrateModel(array $data, TransformerInterface $hydrator): object
    {
        return $hydrator->transform($data);
    }

    /**
     * Retrieves the DB connection.
     */
    abstract protected function getConnection(): PDO;

    /**
     * Retrieves the name of the primary key field.
     */
    abstract protected function getPrimaryKeyFieldName(): string;
}
