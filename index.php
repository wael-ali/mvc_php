<?php
use app\Core\Container;
use app\Core\Application;

define('DS',DIRECTORY_SEPARATOR);
define('APP_ENV','DEV');
//define('APP_ENV','PROD');
define('ROOT', __DIR__ . DS);
define('APP', ROOT.'app'.DS);
define('CONFIG', ROOT.'config'.DS);
define('VIEW', APP.'View'.DS);
define('MODEL', APP.DS.'Model'.DS);
define('DATA', APP.DS.'Data'.DS);
define('CORE', APP.DS.'Core'.DS);
define('CONTROLLER', APP.'Controller'.DS);
define('CONTROLLERS_NAME_SPACE', '\app\Controller\\');


spl_autoload_register(function ($className){
    $path = __DIR__.DS.str_replace('\\', DS,$className).'.php';
    if (file_exists($path)){
        require $path;
    }
});

$container = new Container();
$app = new Application($container);




echo '<pre>';
//print_r($container->getRoutes());
echo '</pre>';
?>
