<?php


namespace app\Command;


use app\Core\ConsoleCommandInterface;
use app\Service\Database;

class DropDbCommand implements ConsoleCommandInterface
{
    private $name = 'mvc:database:drop';
    private $args = [
        '--force'
    ];
    private $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function run()
    {
        return $this->database->dropDatabase();
    }

    public function needConformation(): bool
    {
        return true;
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

}