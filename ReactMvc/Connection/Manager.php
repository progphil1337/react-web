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

    public function getOneBy(string $column, mixed $val): ?object
    {
        $statement = $this->connection->getSQL()->prepare(sprintf('SELECT * FROM `%s` WHERE `%s` = :%s', $this->table, $column, $column));
        $statement->execute([sprintf(':%s', $column) => $val]);

        if ($statement->rowCount() === 0) {
            return null;
        }

        return $this->createEntity($statement->fetch(PDO::FETCH_ASSOC));
    }

    public function getById(int $id): ?object
    {
        return $this->getOneBy('id', $id);
    }

    private function formatToSQLColumnName(string $string): string
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

    private function createEntity(array $row): object
    {
        $reflection = new \ReflectionClass($this->entityClassName);

        if ($this->primaryKey !== null) {
            $instance = $reflection->newInstanceArgs([$row[$this->primaryKey]]);
        } else {
            $instance = $reflection->newInstance();
        }

        foreach ($reflection->getProperties() as $attribute) {
            if ($attribute->name !== $this->primaryKey) {
                $instance->{$attribute->name} = $row[$this->formatToSQLColumnName($attribute->name)];
            }
        }

        return $instance;
    }
}