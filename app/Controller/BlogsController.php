<?php


namespace app\Controller;


use app\Core\Controller;
use app\Service\MailingService;

class BlogsController extends Controller implements AppInterface
{
    private $name = 'home controller ...';
    /**
     * @var MailingService
     */
    private $mailingService;

    public function __construct(MailingService $mailingService)
    {
        $this->mailingService = $mailingService;
    }

    /**
     * @Rout(rout:"/blogs/update",name:"update_blog")
     */
    public function update()
    {
        echo __METHOD__.'<br>';

    }
    /**
     * @Rout(rout:"/blogs",name:"blogs")
     */
    public function index()
    {
        echo __METHOD__.'<br>';

    }

    /**
     * @Rout(rout:"/blogs/create",name:"create-blog")
     */
    public function newBlog()
    {
        echo __METHOD__.'<br>';
    }
    public function __toString()
    {
        return 'BlogsController';
    }

    public function getControllerName()
    {
        // TODO: Implement getControllerName() method.
    }

    public function getAppName()
    {
        // TODO: Implement getAppName() method.
    }
}