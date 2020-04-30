<?php

namespace app\Core;


use Exception;

class Application
{
    protected $controller   = 'HomeController';
    protected $currentRout;
    protected $tempRoutName;
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
        }catch (Exception $e){
            if (!isset($GLOBALS['APP_ENV'])){
                echo '<h1> Sorrey, Something went wrong</h1>';
                return;
            }
            if (($GLOBALS['APP_ENV'] != 'PROD')){
                throw $e;
            }
            echo '<div style="padding: 100px"><h3>
              500 Error: Sorrey, Something went wrong
            </h3></div>';
        }



    }

    protected function loadController(){

        $requestUri = $_SERVER['REQUEST_URI'];

        $isRouteName = preg_replace_callback(
            '/^(\/@@_route_name\?)([a-z-_]+)?/',
            function ($item) {
                if ($item[1] === '/@@_route_name?'){
                    $this->tempRoutName = $item[2];
                    return true;
                }
                $this->tempRoutName = null;
                return false;
            },
            $requestUri
        );

        if ($isRouteName){
            // get route object from the container depending on route name in url and redirect to it.
            foreach ($this->container->getRoutes() as $route){
                if ($this->tempRoutName == $route->getName()){
                    header('location: '.$route->getRout());
                    break;
                }
            }
        }
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
