<?php


namespace app\Core;


use Throwable;

class NotFoundException extends \Exception
{
    private $msg;
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * @param mixed $msg
     */
    public function setMsg($msg)
    {
        $this->msg = $msg;
        $this->message = $msg;
    }
}