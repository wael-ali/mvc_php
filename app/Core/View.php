<?php


namespace app\Core;


class View
{
    protected $templateName ;
    protected $templateData ;
    protected $tempStartForLoopTag;
    protected $tempForLoopVariable;

    public function __construct()
    {
        $this->templateName = 'errors/somethingWentWrong.tpl.html';
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
                $this->templateData['error'] = $GLOBALS['APP_ENV'] !== 'PROD' ?
                    $errorMsg
                    :
                    '500 error'
                ;
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
                $templateContent = $this->replaceStringVariable($key, $value, $templateContent);;
            }elseif (is_object($value)){
                $templateContent = $this->replaceObject($key, $value, $templateContent);
            }elseif (is_array($value) && count($value) > 0){
                $templateContent = $this->replaceArrayVariable($key, $value, $templateContent);
            }
            // else empty array, do nothing
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
            throw new \Exception('No public function ' . $ref->getName() . '.' . $methodNameInTemplate.'in template '.$this->templateName);
        };
        return $returnedValue;
    }

    private function returnForLoopOnStringArray($searchItem, $key,$valuesArray)
    {
        $str_inside_loop = $searchItem;
        // remove start loop tag
        $str_inside_loop  = preg_replace_callback(
            '/{% for ([a-z]|[A-z])+ in '.$key.' %}/',
            function ($s_r) {
                $this->tempStartForLoopTag = $s_r[0];
                return '';
            },
            $str_inside_loop
        );
        // remove end loop tag
        $str_inside_loop  = preg_replace_callback(
            '/{% endfor %}/',
            function (){
                return '';
            },
            $str_inside_loop
        );
        // get the temporary var inside for loop ex: {% for item_name in items_array %} ==> item_name
        $temp_loop_var = preg_replace_callback(
            '/{% for (([a-z]|[A-z])+) in '.$key.' %}/',
            function ($serch){
                $this->tempForLoopVariable = $serch[1];
                return $this->tempStartForLoopTag;
            },
            $this->tempStartForLoopTag
        );

        $temp_str = '';
        foreach ($valuesArray as $value){
            $temp_str .= preg_replace_callback(
                '/{{\s*'.$this->tempForLoopVariable.'*\s*}}/',
                function ($ser) use ($value){
                    return $value;
                },
                $str_inside_loop
            );
        }
        return $temp_str;
    }


    private function returnForLoopOnObjectsArray($searchItem, $key,$valuesArray)
    {
        $str_inside_loop = $searchItem;
        // remove start loop tag
        $str_inside_loop  = preg_replace_callback(
            '/{% for ([a-z]|[A-z])+ in '.$key.' %}/',
            function ($s_r) {
                $this->tempStartForLoopTag = $s_r[0];
                return '';
            },
            $str_inside_loop
        );
        // remove end loop tag
        $str_inside_loop  = preg_replace_callback(
            '/{% endfor %}/',
            function (){
                return '';
            },
            $str_inside_loop
        );
        // get the temporary var inside for loop ex: {% for item_name in items_array %} ==> item_name
        $temp_loop_var = preg_replace_callback(
            '/{% for (([a-z]|[A-z])+) in '.$key.' %}/',
            function ($serch){
                $this->tempForLoopVariable = $serch[1];
                return $this->tempStartForLoopTag;
            },
            $this->tempStartForLoopTag
        );

        $temp_str = '';
        foreach ($valuesArray as $obj){
            if (preg_match('/^{{\s*'.$this->tempForLoopVariable.'\s*}}$/', $str_inside_loop)){
                $temp_str .= $this->replaceObjectVariableIfHasToString($this->tempForLoopVariable, $obj, $str_inside_loop);
                continue;
            }
            $temp_str .= $this->replaceObjectDotAttribute($this->tempForLoopVariable, $obj, $str_inside_loop);
        }
        return $temp_str;
    }

    private function checkArrayType($array): array
    {
        $results = [];
        foreach ($array as $key => $value){
            $results['obj'][] = is_object($value);
            $results['string'][] = is_string($value);
            $results['int'][] = is_integer($value);
            $results['array'][] = is_array($value);
        }
        $type = [] ;
        foreach ($results as $key => $value){
            if (in_array('true', $value)){
                $type[] = $key;
            }
        }
        return $type;
    }

    private function replaceStringVariable(string $varName, string $var, string $templateContent): string
    {
        $newTemplate =  preg_replace_callback(
            '/{{'.$varName.'}}|{{\s+'.$varName.'\s+}}/',
            function ($item) use ($var){
                return $var;
            },
            $templateContent
        );
         return $newTemplate;
    }
    private function replaceObjectVariableIfHasToString(string $key, object $value, string $templateContent): string
    {
         return preg_replace_callback(
             '/{{'.$key.'}}|{{\s+'.$key.'\s+}}/',
             function ($item) use ($value){
                 return (string)$value;
             },
             $templateContent
         );
    }
    private function replaceObjectDotAttribute(string $key, object $value, string $templateContent): string
    {
         return preg_replace_callback(
             '/{{'.$key.'.[a-z]+}}|{{\s+'.$key.'.[a-z]+\s+}}/', // object.methode_name
             function ($item) use ($value){
                 return $this->getKeyValueFromObject($item, $value);
             },
             $templateContent
         );
    }
    private function replaceArrayVariable(string $key, array $value, string $templateContent): string
    {
        // Can not convert Array to string
        $templateContent = preg_replace_callback(
            '/{{'.$key.'}}|{{\s+'.$key.'\s+}}/',
            function ($item) use ($value){
                throw new \Exception('Cannot convert array to string in template '.$this->templateName);
            },
            $templateContent
        );

        // check if string array or object array or associative array
        $arrayType = $this->checkArrayType($value);
        // ['string', 56, new object, 'string']
        if (count($arrayType) > 1){
            dd('Multi type array', $value);
        }elseif (count($arrayType) === 0){
            dd('No support for this array type (an array without type)', $value);
        }elseif (count($arrayType) === 1){
            if ($arrayType[0] === 'string'){
                $templateContent = preg_replace_callback(
                    '/{% for ([a-z]|[A-z])+ in '.$key.' %}.*{% endfor %}/',
                    function ($searchItem) use ($key,$value){
                        $return = $this->returnForLoopOnStringArray($searchItem[0],$key ,$value);
                        return $return;
                    },
                    $templateContent
                );
            }
            if ($arrayType[0] === 'obj'){
                $templateContent = $this->replaceObjectsArrayVariable($key, $value, $templateContent);
            }
        }else{
            dd('$arrayType', $arrayType);
        }
        return $templateContent;
    }
    // array of objects
    private function replaceObjectsArrayVariable(string $key, array $value, string $templateContent): string
    {
        $temp = preg_replace_callback(
            '/{% for ([a-z]|[A-z])+ in '.$key.' %}.*{% endfor %}/',
            function ($searchItem) use ($key, $value){
                $return = $this->returnForLoopOnObjectsArray($searchItem[0], $key,$value);
                return $return;
            },
            $templateContent
        );
        return $temp;
    }

    private function replaceObject(string $key, object $obj, string $templateContent)
    {
        // replace the object with its __tostring if stands alone.
        $templateContent = $this->replaceObjectVariableIfHasToString($key, $obj, $templateContent);
        // replace object.method_name with its value if returns string
        $templateContent = $this->replaceObjectDotAttribute($key, $obj, $templateContent);
        return $templateContent;
    }

}