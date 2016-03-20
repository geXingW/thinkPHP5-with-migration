<?php namespace console;

class Console extends \think\Console
{
    private $availableCommandsList = [
        \think\console\command\Build::class,
        \think\console\command\make\Controller::class,
        \console\commands\scaffolds\migration\InitCommand::class,
        \console\commands\scaffolds\migration\MakeMigrationCommand::class,
        \console\commands\scaffolds\migration\MakeSeederCommand::class,
        \console\commands\scaffolds\migration\MigrateCommand::class,
        \console\commands\scaffolds\migration\SeedRunCommand::class,
        \console\commands\scaffolds\migration\RollBackCommand::class,
        \console\commands\scaffolds\migration\StatusCommand::class
    ];
//
    private $helperCommandsList = [
        \think\console\command\Help::class,
        \think\console\command\Lists::class
    ];
//
    private $commandsList = [];
    private $commandsInstancesList = [];
//
    public function __construct($name, $version)
    {
        parent::__construct($name, $version);
    }

    protected function getDefaultCommands()
    {
        $this->_setCommandInstance();
        return $this->commandsInstancesList;
    }

    /**
     * set command instances
     */
    private function _setCommandInstance()
    {
        $this->_setCommandsList();

        $commandsInstances = [];
        foreach ($this->commandsList as $command) {
            $commandInstance = new $command();
            array_push($commandsInstances, $commandInstance);
        }
        $this->commandsInstancesList = $commandsInstances;
    }

    /**
     * merge command list
     */
    private function _setCommandsList()
    {
        $this->commandsList = array_merge($this->helperCommandsList, $this->availableCommandsList);
    }
}