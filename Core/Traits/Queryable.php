<?php

namespace Core\Traits;
use PDO;

trait Queryable
{
    static protected string|null $tableName = null;

    static protected string $query = '';

    protected array $commands = [];

    static public function select(array $columns = ['*']): static
    {
        static::resetQuery();
        static::$query = "SELECT " . implode(', ', $columns) . "FROM " . static::$tableName. " ";
        $obj = new static;
        $obj->commands[] = 'select';

        return $obj;
    }
    public function update(int $id, array $data)
    {
        foreach ($data as $key => $value) {
            $str = $key . ' = ' . $value;
        }
        static::$query = "UPDATE " . static::$tableName . " SET " . $str . " WHERE id = {$id}";

        $obj = new static;
        $obj->commands[] = 'update';

        return $obj;
    }

    static public function all(): array
    {
        return static::select()->get();
    }

    static public function find(int $id): static|false
    {
        $query = db()->prepare("SELECT * FROM " . static::$tableName . " WHERE id :id");
        $query->bindParam('id', $id);
        $query->execute();

        return $query->fetchObject(static::class);
    }

    static public function findBy(string $column, $value): static|false
    {
        $query = db()->prepare("SELECT * FROM " . static::$tableName . " WHERE $column = :$column");
        $query->bindParam($column, $value);
        $query->execute();

        return $query->fetchObject(static::class);
    }

    static public function create(array $fields): false|int
    {
        $params = static::prepareQueryParams($fields);
        $query = db()->prepare("INSERT INTO " . static::$tableName . " ($params[keys]) VALUES ($params[placeholders])");

        if (!$query->execute($fields)) {
            return false;
        }
        return (int) db()->lastInsertId();
    }

    static public function destroy(int $id): bool
    {
        $query = db()->prepare("DELETE FROM " . static::$tableName . " WHERE id :id)");
        $query->bindParam('id', $id);
        return $query->execute();
    }

    static protected function prepareQueryParams(array $fields): array
    {
        $keys = array_keys($fields);
        $placeholders = preg_filter('/^/', ':', $keys);

        return [
            'keys' => implode(', ' ,$keys),
            'placeholders' => implode(', ', $placeholders)
        ];
    }

    static protected function resetQuery()
    {
        static::$query = '';
    }

    public function get()
    {
        return db()->query(static::$query)->fetchAll(PDO::FETCH_CLASS, static::class);
    }


}