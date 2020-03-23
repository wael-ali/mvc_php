<?php


namespace app\Core;


class View
{
    protected $templateName ;
    protected $templateData ;

    public function __construct()
    {
        $this->templateName = 'errors/somethingWentWrong.phtml';
    }


    public function render($templateName, $templateData = [])
    {
        $ok = true;
        $errorMsg = null;
        try {
            $ok =  $found = file_exists(VIEW.$templateName);
            $errorMsg = $ok ? null : '<i><u>'.$templateName. '</u></i> not found, create this template or correct the name';
        }catch (\Exception $e){
            $errorMsg =  $e->getMessage();
            $ok = false;
        }catch (\Error $e){
            $errorMsg =  $e->getMessage();
            $ok = false;
        }finally{
            if (!$ok){
                $this->templateData['error'] = $errorMsg;
                include VIEW.$this->templateName;

            }else{
                $this->templateName = $templateName;
                $this->templateData = $templateData;
                include VIEW.$this->templateName;
            }
        }
    }
}