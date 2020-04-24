<?php


namespace app\Core;


class Dump
{

    public function dd(array $args){
//    var_dump($args);die();
        foreach ($args as  $arg){
            echo '<pre style="background-color: #262525; color: #9e7713; padding: 10px; font-size: larger">';
            if (is_object($arg) && !is_array($arg)){
                $this->dump_obj($arg);
            }else if (is_array($arg)){
                $this->dump_array($arg);
            }else{
                echo '<div onclick="toggleFirstChild()">';
                echo '<div >';
                var_dump($arg);
                echo '</div>'.
                    '</div>';
            }
            echo '</pre>';
        }

        echo '<script>
        function toggleFirstChild() {
            var clicked = event.target;
            console.log(clicked);
            if (clicked.classList.contains("has-div-container-js")){
                var toggledDiv = event.target.firstElementChild;
                console.log(clicked);
                toggledDiv.style.display = window.getComputedStyle(toggledDiv).display === "none" ? "block" : "none";  
            } 
        }
    </script>';
        die();
    }


    private function obj2array ( &$Instance ) {
        $clone = (array) $Instance;
        $className = get_class($Instance);
//    var_dump($className,$clone,$Instance);die();
        $arr = [];
        $arr['class_name'] = $className;
        foreach ($clone as $key => $value){
//        var_dump($className,$key, strpos($key, $className));die();
            if (strpos($key, $className) !== false) {
                $aux = explode($className,$key);
                $arr['class_vars'][$aux[1]] = $value;
            }else{
                $arr['class_vars'][$key] = $value !== null ? $value : 'null';
            }
        }
        return $arr;
    }

    private function dump_obj($obj){
        $obj_arr = $this->obj2array($obj);
        $id  = 'class:'.$obj_arr['class_name'];
        echo '<div class="has-div-container-js" onclick="toggleFirstChild()">'.$obj_arr['class_name'];
        echo '<div  style="display:block; padding-lift: 40px">';
        foreach ($obj_arr['class_vars'] as $key => $value){
            echo '<div>';
            echo $key.' : ';
            if (is_array($value)){
                $this->dump_array($value);
            }else if (is_object($value)){
                $this->dump_obj($value);
            }else{
                echo $value;
            }
            echo '<div />';
        }
        echo '</div>';
        echo '</div>';
    }
    private function dump_array($array){
        echo '<div class="has-div-container-js" onclick="toggleFirstChild()"> array: ';
        echo '<div style="display: block">';
        if (count($array) === 0){
            echo '<div> array : []</div>';
        }
        foreach ($array as $key => $value){
            echo '<div>';
            echo $key.' : ';
            if (is_array($value)){
                $this->dump_array($value);
            }else if(is_object($value)){
                $this->dump_obj($value);
            }else{
                echo $value;
            }
            echo '</div>';
        }
        echo '</div>'
            .'</div>';
    }

}