<?php


namespace app\Service;


use app\Core\NotFoundException;

class Database
{
    /**
     * @var DbConnection
     */
    private $connection;
    /**
     * @var NotFoundException
     */
    private $exception;

    public function __construct(DbConnection $connection, NotFoundException $exception, $test = ['free','one'])
//    public function __construct( int $num = 100, array $test = ['free','one'])
    {
        $this->connection = $connection;
        $this->exception = $exception;
    }

    public function getDbConnection(){
        return $this->connection;
    }
}