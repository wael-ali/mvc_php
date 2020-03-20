<?php


namespace config;


class GlobalConfiguration
{
    // Loading configrations and globals
    public function LoadConfigrations(){

        $GLOBALS['DS'] = DIRECTORY_SEPARATOR;
        $GLOBALS['APP_ENV'] = 'DEV';
        $GLOBALS['ROOT'] =  __DIR__ . $GLOBALS['DS'];
        $GLOBALS['APP'] =  $GLOBALS['ROOT'].'app'.$GLOBALS['DS'];
        $GLOBALS['CONFIG'] =  $GLOBALS['ROOT'].'config'.$GLOBALS['DS'];
        $GLOBALS['VIEW'] =  $GLOBALS['APP'].$GLOBALS['DS'].'View'.$GLOBALS['DS'];
        $GLOBALS['MODEL'] =  $GLOBALS['APP'].$GLOBALS['DS'].'Model'.$GLOBALS['DS'];
        $GLOBALS['DATA'] =  $GLOBALS['APP'].$GLOBALS['DS'].'Data'.$GLOBALS['DS'];
        $GLOBALS['CORE'] =  $GLOBALS['APP'].$GLOBALS['DS'].'Core'.$GLOBALS['DS'];
        $GLOBALS['CONTROLLER'] =  $GLOBALS['APP'].'Controller'.$GLOBALS['DS'];
        $GLOBALS['CONTROLLERS_NAME_SPACE'] =  '\app\Controller\\';

        echo 'config.................';
    }
}