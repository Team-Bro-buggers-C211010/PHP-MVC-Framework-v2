<?php

namespace Core\Database;

require_once Framework . 'Helper/Helper.php';

use function Helper\isValidIdentifier;


class QueryBuilder
{
    private \PDO $conn;

    private $tableName;

    public function __construct(Database $db, string $tableName)
    {
        $this->conn = $db->getConnection();
        $this->tableName = $tableName;
    }

    // Find all records (READ all)
    public function findAll($queryParams): array
    {
        if (!isValidIdentifier($this->tableName)) {
            throw new \InvalidArgumentException("Invalid table name");
        }

        $query = "SELECT * FROM `$this->tableName`";

        if (count($queryParams) === 0) {
            $stmt = $this->conn->query($query);
            return $stmt ? $stmt->fetchAll() : [];
        }

        $query .= " WHERE ";
        $it = 0;

        foreach ($queryParams as $key => $value) {
            $query .= "`$key` LIKE '%$value%'";
            if($it < count($queryParams) - 1) {
                $query .= " OR ";
            }
            $it++;
        }

        // var_dump($query);
        // die();

        $stmt = $this->conn->query($query);

        return $stmt ? $stmt->fetchAll() : [];
    }

    // Find record by ID (READ one)
    public function findById($id)
    {
        if (!isValidIdentifier($this->tableName)) {
            throw new \InvalidArgumentException("Invalid table name");
        }

        $query = "SELECT * FROM `$this->tableName` WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch();
    }

    // Create a new record (CREATE)
    public function create(array $fieldsAndValues)
    {
        if (!isValidIdentifier($this->tableName)) {
            throw new \InvalidArgumentException("Invalid table name");
        }

        if (empty($fieldsAndValues)) {
            throw new \InvalidArgumentException("Fields and values are required");
        }

        $fields = [];
        $placeholders = [];
        $values = [];

        foreach ($fieldsAndValues as $field => $value) {
            if (!isValidIdentifier($field)) {
                throw new \InvalidArgumentException("Invalid field name: $field");
            }

            $fields[] = "`$field`";
            $placeholders[] = ":$field";
            $values[":$field"] = $value;
        }

        // Use backticks (`) around table and column names to safely escape identifiers.
        // This prevents conflicts with reserved keywords and supports special characters or casing.

        $sql = "INSERT INTO `{$this->tableName}` (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $placeholders) . ")";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt->execute($values)) {
            throw new \Exception("Failed to insert record into {$this->tableName}");
        }

        return $this->conn->lastInsertId(); // Return the new ID for reference
    }

    // Update a record by ID (UPDATE)
    public function update($id, array $fieldsAndValues)
    {
        if (!isValidIdentifier($this->tableName)) {
            throw new \InvalidArgumentException("Invalid table name");
        }

        if (empty($fieldsAndValues)) {
            throw new \InvalidArgumentException("Fields and values are required");
        }

        $sets = [];
        $values = [];

        foreach ($fieldsAndValues as $field => $value) {
            if (!isValidIdentifier($field)) {
                throw new \InvalidArgumentException("Invalid field name: $field");
            }

            $sets[] = "`$field` = :$field";
            $values[":$field"] = $value;
        }

        $values[":id"] = $id;

        $sql = "UPDATE `{$this->tableName}` SET " . implode(', ', $sets) . " WHERE id = :id";
        $stmt = $this->conn->prepare($sql);

        return $stmt->execute($values); // Returns true on success
    }

    // Delete a record by ID (DELETE)
    public function delete($id)
    {
        if (!isValidIdentifier($this->tableName)) {
            throw new \InvalidArgumentException("Invalid table name");
        }

        $sql = "DELETE FROM `{$this->tableName}` WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);

        return $stmt->execute(); // Returns true on success
    }
}
