<?php


namespace app\Core;


class Container
{
    private $controllers = [];
    private $routes = [];
    private $route = [];
    private $notFoundException;

    public function __construct()
    {
        $this->createControllers();
    }

    public function getController($controllerName)
    {
        if (!array_key_exists($controllerName,$this->controllers)){
            $exception = $this->getNotFoundException();
            $exception->setMsg($controllerName.' NOT FOUND!!');
            throw $exception;
        }
        return $this->controllers[$controllerName];
    }

    public function getNotFoundException()
    {
        if (!$this->notFoundException){
            $this->notFoundException = new NotFoundException();
        }
        return $this->notFoundException;
    }

    private function createControllers(){
        $mainDir = CONTROLLER;
        $dirs = [];
        echo '<pre>';
        foreach (new \DirectoryIterator($mainDir) as $fileInfo) {
            if($fileInfo->isDot()) continue;
            if ($fileInfo->isDir()){
                $dirs[$fileInfo->getFilename()] = $fileInfo->getPath();
                continue;
            }
            // creating controller
            $controllerName = (explode('.',$fileInfo->getFilename()))[0];
            $controllerClass = CONTROLLERS_NAME_SPACE.$controllerName;
            $this->controllers[$controllerName] = new $controllerClass();

            // Create routes from comments in controller
            $routDefinitionFound = false;
            $actionDefinitionFound = false;
            $tempRout = [];

            $file = fopen($fileInfo->getRealPath(),"r");
            while(! feof($file))
            {
                $line = fgets($file);
                if (strpos($line, '* @Rout(') !== false) {
                    $tempRout = $this->handleRouteLineDefinition($line);
                    $tempRout['controller'] = $controllerName;
                    $routDefinitionFound = true;
                }
                if (strpos($line, 'function') !== false) {
                    $actionDefinitionFound = true;
                    if ($routDefinitionFound&&$actionDefinitionFound){
                        $tempRout = array_merge($tempRout,$this->handleActionLineDefinition($line));
                        $this->routes[] = (new Rout())->updateRoutFromArray($tempRout);
                        $tempRout = [];
                    }
                    $routDefinitionFound = false;
                    $actionDefinitionFound = false;
                }
            }
            fclose($file);
        }
        echo '</pre>';

        $this->controllers['Error'] = new Error();
    }

    /**
     * @return Rout[]
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    private function handleRouteLineDefinition($line)
    {
        $strarr = explode('(', $line);
        $strarr = explode(')', $strarr[1]);
        $strarr = explode(',',$strarr[0]);
        $tempRout['rout'] = str_replace('"','',(explode(':',$strarr[0]))[1]);
        $tempRout['name'] = str_replace('"','',(explode(':',$strarr[1]))[1]);
        // TODO GET THE METHOD OF ROUTE
        $tempRout['method'] = 'GET';
        return $tempRout;
    }
    private function handleActionLineDefinition($line)
    {
        $actarr = explode(' function ', $line);
        $actarr = explode('(', $actarr[1]);
        $tempRout['action'] = trim($actarr[0]);
        $tempRout['params'] = (explode(')',trim($actarr[1])))[0];
        return $tempRout;
    }

    /**
     * @return array
     */
    public function getRoute()
    {
        if (!$this->route){
            $this->route = new Rout();
        }
        return $this->route;
    }

}