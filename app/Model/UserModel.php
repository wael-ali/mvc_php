<?php


namespace app\Model;


use app\Entity\User;
use app\Service\Database;

class UserModel
{
    private $createTableSql = '
        CREATE TABLE `user`(
                id INT(11) AUTO_INCREMENT PRIMARY KEY,
                firsname VARCHAR(100) NOT NULL,
                lastname VARCHAR(100) NOT NULL,
                reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                email VARCHAR(100) NOT NULL
        )'
    ;
    private $insert = '
            INSERT INTO `table_name`
            (`firsname`, `lastname`, `email`)
             VALUES 
             ("John","Doe","john.doe@example.com")
    ';
    /**
     * @var Database
     */
    private $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function findAll(){
        return $this->db->findAll(User::class);
    }
}