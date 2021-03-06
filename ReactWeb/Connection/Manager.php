<?php

declare(strict_types=1);

namespace ReactWeb\Connection;

use InvalidArgumentException;
use PDO;
use ReactWeb\DependencyInjection\Singleton;

/**
 * Manager
 *
 * @package ReactWeb\Connection
 * @author Philipp Lohmann <lohmann.philipp@gmx.net>
 */
abstract class Manager implements Singleton
{
    public function __construct(
        protected readonly DatabaseConnection $connection,
        protected readonly string $table,
        protected readonly string $entityClassName,
        protected readonly string $primaryKey
    )
    {

    }

    public function save(object $o): bool
    {
        if (!$o instanceof $this->entityClassName) {
            throw new InvalidArgumentException('Cannot save entity in this manager');
        }

        $columnValues = get_object_vars($o);

        $updateStr = '';
        $updateParams = [];
        foreach ($columnValues as $name => $val) {
            $updateStr .= sprintf('`%s` = :%s, ', $this->formatToSQLColumnName($name), $name);
            $updateParams[sprintf(':%s', $name)] = $val;
        }

        $updateStr = substr($updateStr, 0, -2);

        $queryStr = sprintf(
            'UPDATE `%s` SET %s WHERE `%s` = "%s"', $this->table, $updateStr, $this->primaryKey, $o->{$this->formatToEntityColumnName($this->primaryKey)}
        );

        $statement = $this->connection->pdo->prepare($queryStr);
        $statement->execute($updateParams);

        return $statement->rowCount() === 1;
    }


    /**
     * @param string $column
     * @param mixed $val
     * @return object|null
     */
    public function getOneBy(string $column, mixed $val): ?object
    {
        $statement = $this->connection->pdo->prepare(sprintf('SELECT * FROM `%s` WHERE `%s` = :%s', $this->table, $column, $column));
        $statement->execute([sprintf(':%s', $column) => $val]);

        if ($statement->rowCount() === 0) {
            return null;
        }

        return $this->createEntity($statement->fetch(PDO::FETCH_ASSOC));
    }

    /**
     * @param int $id
     * @return object|null
     */
    public function getById(int $id): ?object
    {
        return $this->getOneBy('id', $id);
    }

    /**
     * @param string $string
     * @return string
     */
    protected function formatToSQLColumnName(string $string): string
    {
        $columnName = '';

        foreach (str_split($string) as $char) {
            if ($char === mb_strtoupper($char)) {
                $columnName .= sprintf('_%s', mb_strtolower($char));
            } else {
                $columnName .= $char;
            }
        }

        return $columnName;
    }

    /**
     * @param string $string
     * @return string
     */
    protected function formatToEntityColumnName(string $string): string
    {
        $columnName = '';

        $toUppercase = false;
        foreach (str_split($string) as $char) {
            if ($char === '_') {
                $toUppercase = true;
            } else {
                $columnName .= $toUppercase ? mb_strtoupper($char) : $char;

                $toUppercase = false;
            }
        }

        return $columnName;
    }

    /**
     * @param array $row
     * @return object
     * @throws \ReflectionException
     */
    private function createEntity(array $row): object
    {
        $reflection = new \ReflectionClass($this->entityClassName);

        $constructor = $reflection->getConstructor();

        if ($constructor !== null) {
            $constructorParams = [];

            foreach ($constructor->getParameters() as $parameter) {
                $name = $this->formatToSQLColumnName($parameter->getName());

                $constructorParams[] = $row[$name];
                unset($row[$name]);
            }

            $instance = $reflection->newInstanceArgs($constructorParams);
        } else {
            $instance = $reflection->newInstance();
        }

        foreach ($row as $name => $val) {
            $instance->{$this->formatToEntityColumnName($name)} = $val;
        }

        return $instance;
    }
}