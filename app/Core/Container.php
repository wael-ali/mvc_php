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

    public function __construct()
    {
        $this->createServices();
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

    private function createControllers($mainDir = CONTROLLER){
        echo '<pre>';
        foreach (new \DirectoryIterator($mainDir) as $fileInfo) {
            if($fileInfo->isDot()) continue;
            if ($fileInfo->isDir()){
                $this->createControllers($fileInfo->getPathname());
                continue;
            }
            // creating controller
            $controllerName = (explode('app',$fileInfo->getPathname()))[1];
            $controllerName = 'app'.$controllerName;
            $controllerClass = (explode('.',$controllerName));
            $className = $controllerClass[0];

            try{
                $reflector = new \ReflectionClass($className);
            }catch (\Exception $e){
                dd($controllerName, $controllerClass, $e->getMessage());
            }
            $cont  = $this->getInstanceFromReflection($reflector);
            $this->controllers[$controllerName] = $cont;
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
                        // TODO fetch the argument from services.yaml
                        try{
                            $yam_args = $this->fetchServiceArgumentsFromServiceYaml($ref->getName());
                        }catch (\Exception $e){
                            // TODO make its own Exception
                            throw $e;
                        }
                        if (isset($yam_args[$param->getName()])){
                            $args[] = $yam_args[$param->getName()];
                            continue;
                        }
                        throw new \Exception(
                            'This service: '
                            .$ref->getName()
                            .' can not be instantiated.--- cannt be autowired, define it in serviecs.yaml and add the argument $'
                            .$param->getName()
                            .' and its value to be autowired'
                        );
                    }
                }else{
                    // Parameter is primitive data type (int, array, string ...)
                    if ($param->getClass() == null){
                        if ($param->isOptional()){
                            try{
                                $args[] = $param->getDefaultValue();
                            }catch (\Exception $e){
                                throw $e;
                            }
                        }else{
                            // TODO fetch the argument from services.yaml
                            try{
                                $yam_args = $this->fetchServiceArgumentsFromServiceYaml($ref->getName());
                            }catch (\Exception $e){
                                dd($e);
                            }
                            if (isset($yam_args[$param->getName()])){
                                $args[] = $yam_args[$param->getName()];
                                continue;
                            }
                            // if not in the services.yaml
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
        $obj = null;
        if (array_key_exists($ref->getName(), $this->services)){
            return $this->services[$ref->getName()];
        }
        // TODO AUTOWIRE INTERFACES
        if ($ref->isInterface()){
            /** @var \ReflectionClass $reflection */
            foreach ($this->reflections as $reflection){
                if ($reflection->implementsInterface($ref->getName())){
                    return $this->getInstanceFromReflection($reflection);
                }
            }
            return null;
        }
        if ($ref->getConstructor() == null) {
            $obj = $ref->newInstance();
        } else if (($ref->getConstructor())->getNumberOfParameters() == 0) {
            $obj = $ref->newInstance();
        // Service has  Dependencies in constructor.
        } else {
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
        }
        $this->services[$ref->getName()] = $obj;
        return $obj;
    }
    // $serviceName is the class name
    private function fetchServiceArgumentsFromServiceYaml(string $serviceName)
    {
        $throwException = false;
        $exceptionMsg = 'none';
        $filePath = ROOT.'config'.DS.'services.yaml';

        if(!file_exists($filePath)){
            $throwException = true;
            $exceptionMsg = 'Services.yaml not Found';
        }

        $servicesYamlArray = \yaml_parse_file($filePath);
        if (!$throwException && !isset($servicesYamlArray['services'])){
            $throwException = true;
            $exceptionMsg = 'services.yaml Does not have services entry or No entries under services.';
        }
        $servicesYamlArray = $servicesYamlArray['services'];
        if ( !$throwException && !isset($servicesYamlArray[$serviceName])){
            $throwException = true;
            $exceptionMsg = $serviceName. ': is not defined in serviecs.yaml, or No entries under the service name';
        }

        if ( !$throwException && !isset($servicesYamlArray[$serviceName]['arguments'])){
            $throwException = true;
            $exceptionMsg = $serviceName. ': has no arguments entry in yaml, or NO entries under arguments';
        }

        if ($throwException){
            throw new \Exception($exceptionMsg);
        }

        $arguments = $servicesYamlArray[$serviceName]['arguments'];

        return  $arguments;
    }


}