<?php


namespace app\Core;


interface ConsoleCommandInterface
{
    /**
     * @return string
     */
    public function getName(): string;
    /**
     * @return array
     */
    public function getArgs(): array;

    /**
     * @param array $args
     */
    public function setArgs(array $args);
    public function run();
    public function needConformation(): bool;
}