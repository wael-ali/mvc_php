<?php


namespace app\Service;


use app\Core\NotFoundException;

class Database
{
    use DBTrait;
    const CREATE_TABLE = "CREATE_TABLE";
    const CHANGE_TABLE = "CHANGE_TABLE";
    const DROP_TABLE = "DROP_TABLE";
    const ALTER_TABLE = "ALTER_TABLE";

    const CREATE_DB = "CREATE_DB";
    const DROP_DB = "DROP_DB";

    /**
     * @var DbConnection
     */
    private $connection;
    /**
     * @var NotFoundException
     */
    private $exception;

    public function __construct(DbConnection $connection, NotFoundException $exception, $test = ['free','one'])
    {
        $this->connection = $connection;
        $this->exception = $exception;
    }

    public function getDbConnection(){
        return $this->connection;
    }
    public function getDBtables(){
        /** @var \mysqli $mysqli */
        $mysqli =  $this->getDbConnection()->getMysqli();
        if ($mysqli->connect_errno) {
            $msg =  "Failed to connect to MySQL: " . $mysqli -> connect_error;
            throw new \Exception($msg);
        }
        $query = $mysqli->host_info;
        dd('getDBtables',$query);
    }

    /**
     * @param array $tableConfigs
     * @throws \Exception
     *
     */
    public function createTable(array $tableConfigs){
        /** @var \mysqli $mysqli */
        $mysqli =  $this->getDbConnection()->getMysqli();
        $this->checkDbConnection($mysqli);
        $createTable  = $this->getSql(self::CREATE_TABLE, $tableConfigs);
        $query = $mysqli->query($createTable);
        if (!$query){
            throw new \Exception($mysqli->error);
        }
    }

    public function createDatabase(string $dbName=null){
        $dbName = $dbName ?? $this->connection->getDbname();
        /** @var \mysqli $mysqli */
        $mysqli =  $this->getDbConnection()->getMysqli();
//        $sql  = 'CREATE DATABASE  IF NOT EXISTS'.$dbName;
        $sql  = 'CREATE DATABASE  '.$dbName;
        $query = $mysqli->query($sql);
        if (!$query){
            throw new \Exception($mysqli->error);
        }
        return ['status' => 'success', 'msg' => $dbName.' created successfully.'];
    }
    public function dropDatabase(string $dbName=null){
        $dbName = $dbName ?? $this->connection->getDbname();
        /** @var \mysqli $mysqli */
        $mysqli =  $this->getDbConnection()->getMysqli();
        if ($mysqli->connect_errno) {
            $msg =  "Failed to connect to MySQL: " . $mysqli -> connect_error;
            throw new \Exception($msg);
        }
//        $sql  = 'CREATE DATABASE  IF NOT EXISTS '.$dbName;
        $sql  = 'DROP DATABASE IF EXISTS '.$dbName;
        $query = $mysqli->query($sql);
        if (!$query){
            throw new \Exception($mysqli->error);
        }
        return ['status' => 'success', 'msg' => $dbName.' droped successfully.'];
    }


    private function getSql($command, array $configs){
        $sql = '';
        $makePrimary = 'ALTER TABLE `tests` ADD PRIMARY KEY(`id`)';
        $autoIncriment = 'ALTER TABLE `tests` CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT';
//        dd($configs);
        switch ($command){
            case self::CREATE_TABLE:
                if (!isset($configs['t_name'])){
                    throw new \Exception(__METHOD__.':'.__LINE__.'__" No table name found"');
                }
                if (!isset($configs['t_fields'])){
                    $sql = 'CREATE TABLE IF NOT EXISTS '.$configs["t_name"].'(id int)';
                }
                break;
            case self::CHANGE_TABLE:
                var_dump('change table sql');
                // TODO implement logic
                if (!isset($configs['t_name'])){
                    throw new \Exception('sql builder: no table name found');
                }
                if (!isset($configs['t_fields'])){
                    $sql = 'CREATE TABLE IF NOT EXISTS '.$configs["t_name"].'(id int)';
                }
                break;
            case self::DROP_TABLE:
                var_dump('DROPING  table sql');
                // TODO implement logic
                if (!isset($configs['t_name'])){
                    throw new \Exception('sql builder: no table name found');
                }
                $sql = 'DROP TABLE IF EXISTS  '.$configs["t_name"];
                break;
        }

        return $sql;
    }

    private function checkDbConnection(\mysqli $mysqli)
    {
        if ($mysqli->connect_errno) {
            $msg =  "Failed to connect to MySQL: " . $mysqli -> connect_error;
            throw new \Exception($msg);
        }
    }
}