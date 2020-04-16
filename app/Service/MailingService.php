<?php


namespace app\Service;


use app\Core\NotFoundException;

class MailingService
{
    /**
     * @var DbConnection
     */
    private $from;
    /**
     * @var NotFoundException
     */
    private $exception;

    public function __construct(NotFoundException $exception, $from)
    {
        $this->exception = $exception;
        $this->from = $from;
    }

    public function sentTo($email){
        echo 'this functions sends email to '.$email;
    }
}