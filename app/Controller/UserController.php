<?php


namespace app\Controller;


use app\Core\Controller;
use app\Service\Database;

class UserController extends Controller
{
    private $name = 'home controller ...';
    /**
     * @var Database
     */
    private $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * @Rout(rout:"/users/update",name:"update_user")
     */
    public function update()
    {
        echo __METHOD__.'<br>';

    }
    /**
     * @Rout(rout:"/users",name:"users")
     */
    public function index()
    {
//        dd('some thing ....');
        $this->db->createTable([
            't_name' => 'test',
//            't_columns' => [
//                [
//                    'name' => 'id',
//                    'type' => 'int',
//                    'length' => 11,
//                    'isNull' => false,
//                    'default' => 'none',
//                    'auto_increment' => true,
//                ],
//            ]
        ]);
        $this->db->createDatabase('db_name_for_aaaa');
        echo __METHOD__.'<br>';

    }

    /**
     * @Rout(rout:"/users/create",name:"create-user")
     */
    public function newUser()
    {
        echo __METHOD__.'<br>';
    }
    public function __toString()
    {
        return 'home controller';
    }
}