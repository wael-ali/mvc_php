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

//        $this->redirectTo('/about');
        $this->render('home/index.phtml',[
            'controller' => __CLASS__,
            'colors' => ['green', 'blue', 'grey', 'red']
        ]);
        return;
    }

    /**
     * @Rout(rout:"/about",name:"about")
     */
    public function aboutUs()
    {
        $this->render('home/about.phtml');
    }
    public function __toString()
    {
        return 'home controller';
    }
}