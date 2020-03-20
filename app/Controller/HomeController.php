<?php


namespace app\Controller;


use app\Core\Controller;

class HomeController extends Controller
{
    private $name = 'home controller ...';

    /**
     * @Rout(rout:"/",name:"home-page")
     */
    public function index()
    {
        echo __METHOD__.'<br>';

    }

    /**
     * @Rout(rout:"/about",name:"about")
     */
    public function aboutUs($Var,$lsl)
    {
        echo __METHOD__.'<br>';
    }
    public function __toString()
    {
        return 'home controller';
    }
}