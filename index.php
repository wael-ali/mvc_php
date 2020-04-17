<?php
use app\Core\Container;
use app\Core\Application;
use app\Service\DbConnection;

define('DS',DIRECTORY_SEPARATOR);
define('APP_ENV','DEV');
//define('APP_ENV','PROD');
define('ROOT', __DIR__ . DS);
define('APP', ROOT.'app'.DS);
define('CONFIG', ROOT.'config'.DS);
define('VIEW', APP.'View'.DS);
define('MODEL', APP.'Model'.DS);
define('DATA', APP.'Data'.DS);
define('CORE', APP.'Core'.DS);
define('SERVICES', APP.'Service'.DS);
define('CONTROLLER', APP.'Controller'.DS);
define('DOT_ENV', ROOT.'.env');
define('CONTROLLERS_NAME_SPACE', '\app\Controller\\');
define('SERVICES_NAME_SPACE', '\app\Service\\');

function parseDoteEnv(){
    if (file_exists(DOT_ENV)){
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
    $params = func_get_args();
    echo '<pre>';
    var_dump($params);
    echo '</pre>';
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
// TODO PARSING CONFIGERATION FROM .env FILE TO GLOBAL VARIABLES
parseDoteEnv(ROOT.'.env');

$container = new Container(new DbConnection([
    'host' => 'localhost',
    'username' => 'root',
    'password' => 'root',
    'dbname' => 'mvc_1',
]));
$app = new Application($container);



?>
