<?php 

namespace Core\MVC;

use Core\Database\Database;
use Core\Database\QueryBuilder;

class Model
{
    public $db;
    public $queryBuilder;

    public $tableName;

    public function __construct($tableName)
    {
        $this->db = new Database();
        $this->tableName = $tableName;
        $this->queryBuilder = new QueryBuilder($this->db, $this->tableName);
    }

    // Find all records
    public function find()
    {
        return $this->queryBuilder->findAll();
    }

    // Find record by ID
    public function findById($id)
    {
        return $this->queryBuilder->findById($id);
    }

    // Create a new record
    public function create($data)
    {
        return $this->queryBuilder->create($data);
    }

    // Update a record by ID
    public function update($id, $data)
    {
        return $this->queryBuilder->update($id, $data);
    }

    // Delete a record by ID
    public function delete($id)
    {
        return $this->queryBuilder->delete($id);
    }
}