<?php

declare(strict_types=1);

namespace Clearvue\Test1\Commands;

use Clearvue\Test1\CallbackIterator;
use Clearvue\Test1\Db\IteratorSelectResult;
use Clearvue\Test1\Db\PdoSelectResult;
use Clearvue\Test1\Db\SelectResultInterface;
use Clearvue\Test1\Transform\TransformerInterface;
use Iterator;
use IteratorIterator;
use PDO;

trait ListCommandTrait
{
    /**
     * Retrieve all commands from the current table.
     *
     * @param int|null $perPage
     * @param int $page
     * @return SelectResultInterface<array>
     */
    protected function getAll(string $tableName, ?int $perPage = null, int $page = 0): SelectResultInterface
    {
        $db = $this->getConnection();

        $offset = $page > 0
            ? $page * $perPage
            : 0;
        $limit = $perPage !== null
            ? $perPage
            : PHP_INT_MAX;

        $query = "SELECT SQL_CALC_FOUND_ROWS * FROM `{$tableName}` LIMIT :offset, :limit";
        // This allows PDO to buffer one row at a time in memory
        $statement = $db->prepare($query, [PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL]);
        $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->execute();
        // Total number of rows before limiting
        $totalRows = (int) $db->query('SELECT FOUND_ROWS()')->fetchColumn();

        return new PdoSelectResult($statement, $totalRows);
    }

    protected function hydrateSelected(SelectResultInterface $selectResult): SelectResultInterface
    {
        $hydrator = $this->getHydrator();
        // Ensure iterator
        $iterator = !$selectResult instanceof Iterator
            ? $selectResult
            : new IteratorIterator($selectResult);

        return new IteratorSelectResult(
            new CallbackIterator(
                $iterator,
                function (array $data) use ($hydrator): object {
                    return $hydrator->transform($data);
                }
            ),
            $selectResult->getFoundRowsCount()
        );
    }

    /**
     * Retrieves the connection to use for listing.
     *
     * @return PDO The connection.
     */
    abstract protected function getConnection(): PDO;

    /**
     * Retrieves the DTO hydrator.
     *
     * @return TransformerInterface The transformer used to hydrate DTOs.
     */
    abstract protected function getHydrator(): TransformerInterface;
}
