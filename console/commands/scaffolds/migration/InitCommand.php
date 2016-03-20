<?php namespace console\commands\scaffolds\migration;

use think\console\input\Argument;
use think\console\Input;
use think\console\Output;

/**
 * Created by PhpStorm.
 * User: WangSF
 * Date: 2016/3/16
 * Time: 18:01
 */
class InitCommand extends AbstractMigrationCommand
{
    public function __construct()
    {
        parent::__construct('migrate:init');
    }

    /**
     *  init command configure
     */
    protected function configure()
    {
        $this->setName('migrate:init')
            ->setDescription('Initialize the application for migrate')
            ->addArgument('path', Argument::OPTIONAL, 'Which path should we initialize for migrate?')
            ->setHelp(sprintf(
                '%sInitializes the application for Phinx%s',
                PHP_EOL,
                PHP_EOL
            ));
    }

    /**
     * @param Input $input
     * @param Output $output
     */
    protected function execute(Input $input, Output $output)
    {
        // get the migration path from the config
        $path = $input->getArgument('path');

        if (null === $path) {
            $path = getcwd();
        }

        $path = realpath($path);

        if (!is_writable($path)) {
            throw new \InvalidArgumentException(sprintf(
                'The directory "%s" is not writable',
                $path
            ));
        }

        // Compute the file path
        $fileName = 'phinx.yml'; // TODO - maybe in the future we allow custom config names.
        $filePath = $path . DIRECTORY_SEPARATOR . $fileName;

        if (file_exists($filePath)) {
            throw new \InvalidArgumentException(sprintf(
                'The file "%s" already exists',
                $filePath
            ));
        }

        // load the config template
        if (is_dir(__DIR__ . '/../../../data/Phinx')) {
            $contents = file_get_contents('/../../../data/Phinx/phinx.yml');
        } else {
            $contents = file_get_contents(APP_SCAFFOLD_TEMPLATE_DIR . '/phinx.yml');
        }

        $dbConfig = [
            '$dbAdapter' => 'mysql',    // 仅支持 mysql
            '$dbHost' => $this->getApplicationDatabaseConfig('hostname'),
            '$dbName' => $this->getApplicationDatabaseConfig('database'),
            '$dbUser' => $this->getApplicationDatabaseConfig('username'),
            '$dbPassword' => $this->getApplicationDatabaseConfig('password'),
            '$dbPort' => $this->getApplicationDatabaseConfig('hostport'),
            '$dbCharset' => $this->getApplicationDatabaseConfig('charset')
        ];

        $contents = strtr($contents, $dbConfig);

        if (false === file_put_contents($filePath, $contents)) {
            throw new \RuntimeException(sprintf(
                'The file "%s" could not be written to',
                $path
            ));
        }

        $output->writeln('<info>created</info> .' . str_replace(getcwd(), '', $filePath));
    }
}