<?php


namespace app\Core;


class Database
{
    /**
     * @var DbConnection
     */
    private $connection;

    public function __construct(DbConnection $connection)
    {
        $this->connection = $connection;
    }
    public function getDbConnection(){
        return $this->connection;
    }
}