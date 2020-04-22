<?php


namespace app\Service;


trait DBTrait
{
    private $name = 'this is a trait';

    public function traitFunction(){
        dd('This is form a trait function');
    }


}