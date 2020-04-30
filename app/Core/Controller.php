<?php


namespace app\Core;


abstract class Controller
{
    protected $view;

    public function __construct()
    {
        $this->view = new View();
        return $this->view;
    }

    public function render($templateName, $templateData = [])
    {
        $this->view->render($templateName, $templateData);
    }

    public function redirectToUrl($url)
    {
        $http  = 'http://';
        $https  = 'https://';

        if(strpos($url, $http) !== false){
            header('location: '.$url);
        }else if (strpos($url, $https) !== false){
            header('location: '.$url);
        }else{
            header('location: '.$http.$url);
        }
    }
    public function redirectTo($routeName)
    {
        $route = '@@_route_name?'.$routeName;
       header('location: '.$route);
    }
}