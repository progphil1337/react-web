<?php

declare(strict_types=1);

namespace ReactMvc\Connection;

use PDO;
use ReactMvc\DependencyInjection\Singleton;

/**
 * Manager
 *
 * @package ReactMvc\Connection
 * @author Philipp Lohmann <philipp.lohmann@check24.de>
 * @copyright CHECK24 GmbH
 */
abstract class Manager implements Singleton
{
    public function __construct(
        protected readonly DatabaseConnection $connection,
        protected readonly string $table,
        protected readonly string $entityClassName,
        protected readonly ?string $primaryKey = null
    )
    {

    }

    /**
     * @param string $column
     * @param mixed $val
     * @return object|null
     * @throws \ReflectionException
     */
    public function getOneBy(string $column, mixed $val): ?object
    {
        $statement = $this->connection->getSQL()->prepare(sprintf('SELECT * FROM `%s` WHERE `%s` = :%s', $this->table, $column, $column));
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
            if ($char === strtoupper($char)) {
                $columnName .= sprintf('_%s', strtolower($char));
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
                $columnName .= $toUppercase ? strtoupper($char) : $char;

                $toUppercase = false;
            }
        }

        return $columnName;
    }

    /**
     * @param array<string,mixed> $row
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