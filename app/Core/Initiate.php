<?php


namespace app\Core;


use Exception;

class Initiate
{
    public function initiateEnv(){
        $this->defineGlobals();
        $this->parseDoteEnv();
//        dd($GLOBALS['APP_ENV']);
    }

    private function defineGlobals(){

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
    }



// PARSING CONFIGERATION FROM .env FILE TO GLOBAL VARIABLES
    private function parseDoteEnv(){
//        var_dump(DOT_ENV);die();
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
}