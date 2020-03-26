<?php


namespace app\Core;


use app\Service\DbConnection;

class Container
{
    private $controllers = [];
    private $services = [];
    private $reflections = [];
    private $routes = [];
    private $route = [];
    private $notFoundException;
    /**
     * @var DbConnection
     */
    private $dbConnection;

    public function __construct(DbConnection $dbConnection)
    {
        $this->addToReflections($dbConnection, DbConnection::class);
        $this->addToServices($dbConnection, DbConnection::class);
        $this->createServices();
        $this->createControllers();
        $this->dbConnection = $dbConnection;
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
    public function getService($serviceName)
    {
        if (!array_key_exists($serviceName,$this->services)){
            $exception = $this->getNotFoundException();
            $exception->setMsg($serviceName.' NOT FOUND!!');
            throw $exception;
        }
        return $this->services[$serviceName];
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

    private function createServices(){
        // Create reflictions of all services in SERVICES and save them in reflictions array.
        $this->updateServicesReflictions();
        // Create instances of all reflections and save them in services array.
        $this->createServicesFromReflections();
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
    private function addToServices($serviceObj, $className)
    {
        $serviceName = str_replace('\\','.',$className);
        if (!array_key_exists($className, $this->services)){
            $this->services[$className] = $serviceObj;
        }
    }
    private function addToReflections($serviceObj, $className)
    {
//        $serviceName = str_replace('\\','.',$className);
        if (!array_key_exists($className, $this->reflections)){
            $this->reflections[$className] = new \ReflectionClass($className);
        }
    }

    private function getDependenciesAsReflections(\ReflectionClass $ref)
    {
        $args = [];
        if ($ref->getConstructor() == null){
            return null;
        }else if (($ref->getConstructor())->getNumberOfParameters()== 0){
            return null;
        }else{
            $constructor_params =  ($ref->getConstructor())->getParameters();
            foreach ($constructor_params as $param){
                // Not type hinted parameter
                if (!$param->hasType()){
                    if ($param->isOptional()){
                        $args[] = $param->getDefaultValue();
                    }else{
                        throw new \Exception($param->getName()
                            .' is not optional, '.$ref->getName()
                            .' can not be instantiated.--- cannt be autowired'
                        );
                    }
                }else{
                    // Parameter is primitive data type (int, array, string ...)
                    if ($param->getClass() == null){
                        if ($param->isOptional()){
                            try{
                                $args[] = $param->getDefaultValue();
                            }catch (\Exception $e){
                                dd($e->getMessage());
                            }
                        }else{
                            throw new \Exception($param->getName()
                                .' is not optional, '.$ref->getName()
                                .' can not be instantiated.--- cannt be autowired'
                            );
                        }
                    }else{
                        $args[] = $param->getClass();
                    }
                }
            }
            return $args;
        }
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

    private function updateServicesReflictions()
    {
        $mainDir = SERVICES;
        $dirs = [];
        echo '<pre>';
        foreach (new \DirectoryIterator($mainDir) as $fileInfo) {
            if ($fileInfo->isDot()) continue;
            if ($fileInfo->isDir()) {
                $dirs[$fileInfo->getFilename()] = $fileInfo->getPath();
                continue;
            }
            $className = (explode('.', $fileInfo->getFilename()))[0];
            $serviceClass = SERVICES_NAME_SPACE . $className;
            // is the service created
            if (array_key_exists($serviceClass, $this->reflections)) {
                continue;
            }
            $reflector = new \ReflectionClass($serviceClass);
            $this->reflections[$reflector->getName()] = $reflector;
        }
    }

    private function createServicesFromReflections()
    {
        /** @var \ReflectionClass $ref */
        foreach ($this->reflections as $ref) {
            // If service instance is already created go for the next reflection object
            if (array_key_exists($ref->getName(), $this->services)) {
                continue;
            }
            // The service instace is not in services => create instance from reflection
            $service = $this->getInstanceFromReflection($ref);
        }
    }

    /**
     * @param \ReflectionClass $ref
     */
    private function getInstanceFromReflection(\ReflectionClass $ref)
    {
//        var_dump('first line ---', $ref->getName(),$ref->isInterface());
        if (array_key_exists($ref->getName(), $this->services)){
            return $this->services[$ref->getName()];
        }
        // TODO AUTOWIRE INTERFACES
        if ($ref->isInterface()){
            return null;
        }
        if ($ref->getConstructor() == null) {
            $obj = $ref->newInstance();
            $this->services[$ref->getName()] = $obj;
            return $obj;
        } else if (($ref->getConstructor())->getNumberOfParameters() == 0) {
            $obj = $ref->newInstance();
            $this->services[$ref->getName()] = $obj;
            return $obj;
        // Service has  Dependencies in constructor.
        } else {
            $obj = null;
            // Get Dependencies as reflections array
            $args = $this->getDependenciesAsReflections($ref);
            $argsInstances = [];

            foreach ($args as $argRef){
                if (!$argRef || !($argRef instanceof \ReflectionClass)){
                    $argsInstances[] = $argRef;
                    continue;
                }
                $argsInstances[] = $this::getInstanceFromReflection($argRef);
            }
            $obj = $ref->newInstanceArgs($argsInstances);
            $this->services[$ref->getName()] = $obj;
            return $obj;
        }
    }

}