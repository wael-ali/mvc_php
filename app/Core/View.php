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
                $this->show();

            }else{
                $this->templateName = $templateName;
                $this->templateData = $templateData;
                $this->show();
            }
        }
    }

    private function show()
    {
        $templateContent = file_get_contents(VIEW.$this->templateName);
        foreach ($this->templateData as $key => $value){
            if (is_string($value)){
                $templateContent = preg_replace_callback(
                    '/{{'.$key.'}}|{{\s+'.$key.'\s+}}/',
                    function ($item) use ($value){
                        return $value;
                    },
                    $templateContent
                    )
                ;
            }elseif (is_object($value)){
                // replace the object with its __tostring if stands alone.
                $templateContent = preg_replace_callback(
                    '/{{'.$key.'}}|{{\s+'.$key.'\s+}}/',
                    function ($item) use ($value){
                        return (string)$value;
                    },
                    $templateContent
                );
                // replace object.method_name with its value if returns string
                $templateContent = preg_replace_callback(
                    '/{{'.$key.'.[a-z]+}}|{{\s+'.$key.'.[a-z]+\s+}}/', // object.methode_name
                    function ($item) use ($value){
                        return $this->getKeyValueFromObject($item, $value);
                    },
                    $templateContent
                );


            }elseif (is_array($value)){
                dd('is array: ', $value);
            }
        }
        echo $templateContent;
    }

    /**
     * @param $foundKey array result of preg_replace_callback
     * @param $value object
     * @return mixed|null
     */
    private function getKeyValueFromObject($foundKey, $keyObject)
    {
        $itemArr = explode('.', $foundKey[0]);
        $method = explode('}}', $itemArr[1]);
        $methodNameInTemplate = 'get' . trim(strtolower($method[0]));
        $ref = new \ReflectionObject($keyObject);
        $returnedValue = null;
        foreach ($ref->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            $isFound = strtolower($method->getName()) === $methodNameInTemplate;
            if ($isFound) {
                $returnedValue = $method->invoke($keyObject);
            }
        }
        if (!$returnedValue) {
            throw new \Exception('No public function found in ' . $ref->getName() . '.' . $methodNameInTemplate);
        };
        return $returnedValue;
    }

}