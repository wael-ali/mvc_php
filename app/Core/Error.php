<?php


namespace app\Core;


class Error extends Controller
{

    public function handle()
    {
        try{
            if ($GLOBALS['APP_ENV'] != "PROD"){
                $this->notFoundDEV();
            }else{
                $this->notFoundProd();
            }
        }catch (\Error $e){
            dd('handle', $e);
        }

    }

    private function notFoundProd()
    {
        $this->render('errors/undefinedRoute.tpl.html', [
            'msg' => '404 Not found...',
        ]);
    }
    private function notFoundDEV()
    {
        $this->render('errors/undefinedRoute.tpl.html', [
            'msg' => 'This route is not defined, change this template @: View/errors/undefinedRoute.tpl.html',
        ]);
    }
}