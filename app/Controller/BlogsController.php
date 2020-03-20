<?php


namespace app\Controller;


class BlogsController
{
    private $name = 'home controller ...';


    /**
     * @Rout(rout:"/blogs/update",name:"update_blog")
     */
    public function update()
    {
        echo __METHOD__.'<br>';

    }
    /**
     * @Rout(rout:"/blogs",name:"blogs")
     */
    public function index()
    {
        echo __METHOD__.'<br>';

    }

    /**
     * @Rout(rout:"/blogs/create",name:"create-blog")
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