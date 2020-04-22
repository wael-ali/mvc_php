<?php
include_once 'kern.php';
use app\Core\Application;



try{
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
}



?>
