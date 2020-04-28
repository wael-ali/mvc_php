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
        echo '<h1>404 Not Found!!</h1>';
    }
    private function notFoundDEV()
    {
        $this->render('errors/undefinedRoute.phtml', [
            'msg' => 'change me at: View/errors/undefinedRoute.phtml',
        ]);
    }
}