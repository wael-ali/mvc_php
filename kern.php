<?php
use app\Core\Container;
use app\Core\Dump;
use app\Core\Initiate;

define('DS',DIRECTORY_SEPARATOR);
define('ROOT',  __DIR__ . DS);

spl_autoload_register(function ($className){
    $path = __DIR__.DIRECTORY_SEPARATOR.str_replace('\\', DIRECTORY_SEPARATOR,$className).'.php';
    if (file_exists($path)){
        require $path;
    }else{
        var_dump('spl_autoload_register',$className, 'path: ',$path);die();

    }
});


function dd(){
    $trace = debug_backtrace(2, true);
    echo '<div style="background-color: #262525; color: green; padding: 10px; font-size: larger">'
        .$trace[0]["file"]
        .':'
        .$trace[0]["line"]
        .'</div>'
    ;
    $args = func_get_args();

    echo '<pre>';
        var_dump($args); die();
    echo '</pre>';
//    $dump = new Dump();
//    $dump->dd($args);
}

try{
    $init = new Initiate();
    $init->initiateEnv();
    $container = new Container();
}catch (Exception $e){
    if (!isset($GLOBALS['APP_ENV'])){
        echo '<h1> Sorrey, Something went wrong</h1>';
        return;
    }
    if (($GLOBALS['APP_ENV'] != 'PROD')){
        throw $e;
    }

    echo '<h1> Sorrey, Something went wrong</h1>';
}



?>
