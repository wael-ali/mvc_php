<?php


namespace app\Core;


class Rout
{
    const ROUT          = "rout";
    const NAME          = "name";
    const METHOD        = "method";
    const CONTROLLER    = "controller";
    const ACTION        = "action";
    const PARAMS        = "param";
    const WHITEE_LIST   = [
        self::ROUT,
        self::NAME,
        self::METHOD,
        self::CONTROLLER,
        self::ACTION,
        self::PARAMS,
    ];


    private $rout;
    private $name;
    private $method;
    private $controller;
    private $action;
    private $params;

    public function __construct()
    {
        $this->rout = '/not-found';
        $this->name = 'not-found';
        $this->method = 'GET';
        $this->controller = 'Error';
        $this->action = 'handle';
        $this->params = [];
    }

    /**
     * @return mixed
     */
    public function getRout()
    {
        return $this->rout;
    }

    /**
     * @param mixed $rout
     */
    public function setRout($rout)
    {
        $this->rout = $rout;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param mixed $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * @return mixed
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * @param mixed $controller
     */
    public function setController($controller)
    {
        $this->controller = $controller;
    }

    /**
     * @return mixed
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param mixed $action
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * @return mixed
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param mixed $param
     */
    public function setParam($params)
    {
        $this->params = $params;
    }

    public function updateRoutFromArray(array $array)
    {
        foreach ($array as $key => $value){
            if (in_array($key, self::WHITEE_LIST)){
                $this->$key = $value;
            }
        }
        return $this;
    }

}