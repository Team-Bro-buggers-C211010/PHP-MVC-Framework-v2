<?php

namespace Core\Database;

class DatabaseBuilder
{
    private \PDO $conn;

    private $tableName;

    public function __construct(Database $db, string $tableName)
    {
        $this->conn = $db->getConnection();
        $this->tableName = $tableName;
    }

    public function findAll(): array
    {
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $this->tableName)) {
            throw new \InvalidArgumentException("Invalid table name");
        }

        $query = "SELECT * FROM `$this->tableName`";
        $stmt = $this->conn->query($query);

        return $stmt ? $stmt->fetchAll() : [];
    }

    public function create(array $fieldsAndValues)
    {
        if (!preg_match("/^[a-zA-Z0-9_]+$/", $this->tableName)) {
            throw new \InvalidArgumentException("Invalid table name");
        }

        if (empty($fieldsAndValues)) {
            throw new \InvalidArgumentException("Fields and values are required");
        }

        $fields = [];
        $placeholders = [];
        $values = [];

        foreach ($fieldsAndValues as $field => $value) {
            if (!preg_match('/^[a-zA-Z0-9_]+$/', $field)) {
                throw new \InvalidArgumentException("Invalid field name: $field");
            }

            $fields[] = "`$field`";
            $placeholders[] = ":$field";
            $values[":$field"] = $value;
        }

        echo '<pre>';
        var_dump($fields);
        echo '</pre>';
        die();

        $sql = "INSERT INTO `{$this->tableName}` (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $placeholders) . ")";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt->execute($values)) {
            throw new \Exception("Failed to insert record into {$this->tableName}");
        }

        return $this->conn->lastInsertId(); // Optional: return the inserted ID
    }
}
