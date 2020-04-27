<?php


namespace app\Command;


use app\Core\ConsoleCommandInterface;
use app\Core\Container;
use app\Service\Database;

class CreateDbCommand implements ConsoleCommandInterface
{
    private $name = 'mvc:database:create';
    private $args = [];
    private $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function run(Container $container = null): array
    {
       return $this->database->createDatabase();
    }
    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }


    /**
     * @return array
     */
    public function getArgs(): array
    {
        return $this->args;
    }

    /**
     * @param array $args
     */
    public function setArgs(array $args)
    {
        $this->args = $args;
    }

    public function needConformation(): bool
    {
        return false;
    }
}