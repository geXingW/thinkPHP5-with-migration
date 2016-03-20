<?php namespace console\commands;

use think\console\command\Command;
use think\console\Input;
use think\console\Output;

class BaseCommand extends Command
{
    /**
     * BaseCommand constructor.
     * @param null|string $commandName
     */
    public function __construct($commandName)
    {
        parent::__construct($commandName);
    }

    /**
     * @param Input $input
     * @param Output $output
     */
    protected function execute(Input $input, Output $output)
    {
        throw new \LogicException('You must override the execute() method in the concrete command class.');
    }
}
