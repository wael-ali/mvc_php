<?php
use app\Core\Container;
use app\Core\Application;

define('DS',DIRECTORY_SEPARATOR);
define('APP_ENV','DEV');
//define('APP_ENV','PROD');
define('ROOT',  __DIR__ . DS);
define('APP',   ROOT.'app'.DS);
define('CONFIG',ROOT.'config'.DS);
define('VIEW',  APP.'View'.DS);
define('MODEL', APP.'Model'.DS);
define('DATA',  APP.'Data'.DS);
define('CORE',  APP.'Core'.DS);
define('SERVICES',   APP.'Service'.DS);
define('CONTROLLER', APP.'Controller'.DS);
define('DOT_ENV',    ROOT.'.env');
define('CONTROLLERS_NAME_SPACE', '\app\Controller\\');
define('SERVICES_NAME_SPACE', '\app\Service\\');

// PARSING CONFIGERATION FROM .env FILE TO GLOBAL VARIABLES
function parseDoteEnv(){
    if (file_exists(DOT_ENV)){
        // default application environment.
        $GLOBALS['APP_ENV'] = 'PROD';
        $envArray = [];
        $fileLines = file(DOT_ENV);
        foreach ($fileLines as $fileLine){
            // escape empty lines
            $fileLine = trim($fileLine);
            if ($fileLine == ''){
                continue;
            }

            if (!preg_match('/^[A-Z][A-Z,_]+=([^\s])+$/',$fileLine)){
                continue;
            }
            $varValue = explode('=',$fileLine);
            $envArray[$varValue[0]] = $varValue[1];
        }
        foreach ($envArray as $var => $value){
            $GLOBALS[$var] = $value;
        }
        return 1;
    }
    throw new Exception('.env file is not found in app root!.');
}

function dd(){
    $trace = debug_backtrace(2, true);
    echo '<div style="background-color: #262525; color: green; padding: 10px; font-size: larger">'
            .$trace[0]["file"]
            .':'
            .$trace[0]["line"]
            .'</div>'
    ;
    $args = func_get_args();
    foreach ($args as $arg){
        echo '<pre style="background-color: #262525; color: #9e7713; padding: 10px; font-size: larger">';
//        if (is_object($arg)){
//            $obj_vars = get_class_vars($arg);
//            foreach ($obj_vars as $var => $value){
//                echo '<li>'.$var .'=>'.$value.'</li>';
//            }
//        }
         var_dump($arg);
        echo '</pre>';
    }
    die();
}
spl_autoload_register(function ($className){
    $path = __DIR__.DS.str_replace('\\', DS,$className).'.php';
    if (file_exists($path)){
        require $path;
    }else{
        dd('spl_autoload_register',$className, 'path: ',$path);

    }
});

try{

    parseDoteEnv();
    $container = new Container();
    $app = new Application($container);
}catch (Exception $e){
    if (!isset($GLOBALS['APP_ENV'])){
        echo '<h1> Sorrey, Something went wrong</h1>';
        return;
    }
    if (($GLOBALS['APP_ENV'] != 'PROD')){
        throw $e;
    }

    echo '<h1> Sorrey, Something went wrong</h1>';
//    dd('some exception', $e->getMessage());
}



?>
