<?php

namespace app\Core;


class Application
{
    protected $controller   = 'HomeController';
    protected $currentRout;
    /**
     * @var Container
     */
    private $container;
    private $notFoundRout = [
            "rout"      => "/not-found",
            "name"      => "not-found",
            "method"    => "GET",
            "controller" => "Error",
            "action"    => "handle",
            "params"    => [],
    ];

    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->loadController();
        $action = $this->currentRout->getAction();
        try{
            $this->controller->$action();
        }catch (\Error $e){
            echo '<pre>';
            var_dump($e, '------------------------');
            echo '</pre>';
        }
    }

    protected function loadController(){
        $requestUri = $_SERVER['REQUEST_URI'];
        foreach ($this->container->getRoutes() as $route){
            if ($requestUri == $route->getRout()){
                $this->currentRout  = $route;
                break;
            }
        }
        if ($this->currentRout){
            try{
                $this->controller = $this->container->getController($this->currentRout->getController());
            }catch (NotFoundException $e){
                $this->currentRout->updateRoutFromArray($this->notFoundRout);
                $this->controller = $this->container->getController($this->currentRout->getController());
            }
        }else{
            $this->currentRout = $this->container->getRoute();
            $this->controller = $this->container->getController($this->currentRout->getController());
        }
    }
}