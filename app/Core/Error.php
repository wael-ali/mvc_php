<?php


namespace app\Core;


class Error
{

    public function handle()
    {
        try{
            if (APP_ENV == "DEV"){
            $this->notFoundDEV();
        }else{
            $this->notFoundProd();
        }
        }catch (\Error $e){

        }

    }

    public function notFoundProd()
    {
        echo '<h1>404 Not Found!!</h1>';
    }
    public function notFoundDEV()
    {
        echo '<h1>404 Not Found!! DEV </h1><br> <h4>This Route is NOt Defined!!</h4>';

    }
}