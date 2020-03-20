<?php


namespace app\Controller;


use app\Core\Controller;

class UserController extends Controller
{
    private $name = 'home controller ...';


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