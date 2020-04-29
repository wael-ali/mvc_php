<?php


namespace app\Controller;


use app\Core\Controller;
use app\Entity\User;
use app\Service\Database;

class UserController extends Controller
{
    private $name = 'home controller ...';
    /**
     * @var Database
     */
    private $database;

    public function __construct(Database $database)
    {
        parent::__construct();
        $this->database = $database;
    }

    /**
     * @Rout(rout:"/users",name:"users")
     */
    public function index()
    {

        $users = $this->database->findAll(User::class);

        $this->render('user/index.tpl.html', [
            'cols' => ['green', 'blue', 'yellow', 'grey', 'red'],
            'items' => ['car', 'house', 'school', 'factory', 'street'],
//            'cols' => [22, 33, 44, 55, 66, 77],
            'users' =>  $users,
            'name' =>  'Wael ali',
            'user' => $users[0]
        ]);

    }

    /**
     * @Rout(rout:"/users/create",name:"create-user")
     */
    public function newUser()
    {
        echo __METHOD__.'<br>';
    }


    /**
     * @Rout(rout:"/users/update",name:"update_user")
     */
    public function update()
    {
        echo __METHOD__.'<br>';

    }

    public function __toString()
    {
        return 'home controller';
    }
}