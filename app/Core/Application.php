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
        try{
            call_user_func_array([$this->controller, $this->currentRout->getAction()], $this->currentRout->getParams());
        }catch (\Error $e){
            echo '<pre>';
            echo $e->getMessage();
            echo '</pre>';
        }catch (\Exception $e){
            echo $e->getMessage();
        }
    }

    protected function loadController(){

        $requestUri = $_SERVER['REQUEST_URI'];
        // get route object from the container depending on url
        foreach ($this->container->getRoutes() as $route){
            if ($requestUri == $route->getRout()){
                $this->currentRout  = $route;
                break;
            }
        }
        // Route is found. load the controller.
        if ($this->currentRout){
            try{
                $this->controller = $this->container->getController($this->currentRout->getController());
            }catch (NotFoundException $e){
                $this->currentRout->updateRoutFromArray($this->notFoundRout);
                $this->controller = $this->container->getController($this->currentRout->getController());
            }catch (\Exception $e){
                echo $e->getMessage();
            }
        // NO matching route is found. load the not found Route and controller.
        }else{
            $this->currentRout = $this->container->getRoute();// default route is the notfound route
            $this->controller = $this->container->getController($this->currentRout->getController());
        }
    }
}
