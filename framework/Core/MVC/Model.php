<?php 

namespace Core\MVC;

use Core\Database\Database;
use Core\Database\DatabaseBuilder;


class Model
{
    public $db;
    public $databaseBuilder;

    public $tableName;



    public function __construct($tableName)
    {
        $this->db = new Database();
        $this->tableName = $tableName;
        $this->databaseBuilder = new DatabaseBuilder($this->db, $this->tableName);
    }

    public function find ()
    {
        return $this->databaseBuilder->findAll();
    }

    public function create ($data)
    {
        $this->databaseBuilder->create($data);
    }
}