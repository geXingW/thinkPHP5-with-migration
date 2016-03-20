<?php namespace console\commands\scaffolds\migration;

use console\commands\BaseCommand;
use think\phinx\symfony\config\FileLocator;
use think\console\Input;
use think\console\Output;
use think\console\input\Option;
use think\phinx\config\Config;
use think\phinx\config\ConfigInterface;
use think\phinx\migration\Manager;
use think\phinx\db\adapter\AdapterInterface;

/**
 * Created by PhpStorm.
 * User: WangSF
 * Date: 2016/3/17
 * Time: 18:00
 */
abstract class AbstractMigrationCommand extends BaseCommand
{
    /**
     * The location of the default migration template.
     */
    const DEFAULT_MIGRATION_TEMPLATE = '/Migration.template.php.dist';

    /**
     * The location of the default seed template.
     */
    const DEFAULT_SEED_TEMPLATE = '/Seeder.template.php.dist';

    /**
     * @var ConfigInterface
     */
    protected $config;

    /**
     * @var AdapterInterface
     */
    protected $adapter;

    /**
     * @var Manager
     */
    protected $manager;

    public function __construct($commandName)
    {
        parent::__construct($commandName);
    }


    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->addOption('--configuration', '-c', Option::VALUE_REQUIRED, 'The configuration file to load');
        $this->addOption('--parser', '-p', Option::VALUE_REQUIRED, 'Parser used to read the config file. Defaults to YAML');
    }

    /**
     * Bootstrap Phinx.
     *
     * @param Input $input
     * @param Output $output
     * @return void
     */
    public function bootstrap(Input $input, Output $output)
    {
        if (!$this->getConfig()) {
            $this->loadConfig($input, $output);
        }

        $this->loadManager($output);
        // report the paths
        $output->writeln('<info>using migration path</info> ' . $this->getConfig()->getMigrationPath());
        try {
            $output->writeln('<info>using seed path</info> ' . $this->getConfig()->getSeedPath());
        } catch (\UnexpectedValueException $e) {
            // do nothing as seeds are optional
        }
    }

    /**
     * Sets the config.
     *
     * @param  ConfigInterface $config
     * @return AbstractCommand
     */
    public function setConfig(ConfigInterface $config)
    {
        $this->config = $config;
        return $this;
    }

    /**
     * Gets the config.
     *
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Sets the database adapter.
     *
     * @param AdapterInterface $adapter
     * @return AbstractCommand
     */
    public function setAdapter(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
        return $this;
    }

    /**
     * Gets the database adapter.
     *
     * @return AdapterInterface
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * Sets the migration manager.
     *
     * @param Manager $manager
     * @return AbstractCommand
     */
    public function setManager(Manager $manager)
    {
        $this->manager = $manager;
        return $this;
    }

    /**
     * Gets the migration manager.
     *
     * @return Manager
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * Returns config file path
     *
     * @param Input $input
     * @return string
     */
    protected function locateConfigFile(Input $input)
    {
        $configFile = $input->getOption('configuration');

        $useDefault = false;

        if (null === $configFile || false === $configFile) {
            $useDefault = true;
        }

        $cwd = getcwd();

        // locate the phinx config file (default: phinx.yml)
        // TODO - In future walk the tree in reverse (max 10 levels)
        $locator = new FileLocator(array(
            $cwd . DIRECTORY_SEPARATOR
        ));

        if (!$useDefault) {
            // Locate() throws an exception if the file does not exist
            return $locator->locate($configFile, $cwd, $first = true);
        }

        $possibleConfigFiles = array('phinx.php', 'phinx.json', 'phinx.yml');
        foreach ($possibleConfigFiles as $configFile) {
            try {
                return $locator->locate($configFile, $cwd, $first = true);
            } catch (\InvalidArgumentException $exception) {
                $lastException = $exception;
            }
        }
        throw $lastException;
    }

    /**
     * Parse the config file and load it into the config object
     *
     * @param Input $input
     * @param Output $output
     * @throws \InvalidArgumentException
     * @return void
     */
    protected function loadConfig(Input $input, Output $output)
    {
        $configFilePath = $this->locateConfigFile($input);
        $output->writeln('<info>using config file</info> .' . str_replace(getcwd(), '', realpath($configFilePath)));

        $parser = $input->getOption('parser');

        // If no parser is specified try to determine the correct one from the file extension.  Defaults to YAML
        if (null === $parser) {
            $extension = pathinfo($configFilePath, PATHINFO_EXTENSION);

            switch (strtolower($extension)) {
                case 'json':
                    $parser = 'json';
                    break;
                case 'php':
                    $parser = 'php';
                    break;
                case 'yml':
                default:
                    $parser = 'yaml';
            }
        }

        switch (strtolower($parser)) {
            case 'json':
                $config = Config::fromJson($configFilePath);
                break;
            case 'php':
                $config = Config::fromPhp($configFilePath);
                break;
            case 'yaml':
                $config = Config::fromYaml($configFilePath);
                break;
            default:
                throw new \InvalidArgumentException(sprintf('\'%s\' is not a valid parser.', $parser));
        }

        $output->writeln('<info>using config parser</info> ' . $parser);

        $this->setConfig($config);
    }

    /**
     * Load the migrations manager and inject the config
     *
     * @param Output $output
     * @return void
     */
    protected function loadManager(Output $output)
    {
        if (null === $this->getManager()) {
            $manager = new Manager($this->getConfig(), $output);
            $this->setManager($manager);
        }
    }

    /**
     * Verify that the migration directory exists and is writable.
     *
     * @throws InvalidArgumentException
     * @return void
     */
    protected function verifyMigrationDirectory($path)
    {
        if (!is_dir($path)) {
            throw new \InvalidArgumentException(sprintf(
                'Migration directory "%s" does not exist',
                $path
            ));
        }

        if (!is_writable($path)) {
            throw new \InvalidArgumentException(sprintf(
                'Migration directory "%s" is not writable',
                $path
            ));
        }
    }

    /**
     * Verify that the seed directory exists and is writable.
     *
     * @throws InvalidArgumentException
     * @return void
     */
    protected function verifySeedDirectory($path)
    {
        if (!is_dir($path)) {
            throw new \InvalidArgumentException(sprintf(
                'Seed directory "%s" does not exist',
                $path
            ));
        }

        if (!is_writable($path)) {
            throw new \InvalidArgumentException(sprintf(
                'Seed directory "%s" is not writable',
                $path
            ));
        }
    }

    /**
     * Returns the migration template filename.
     *
     * @return string
     */
    protected function getMigrationTemplateFile()
    {
        return APP_SCAFFOLD_TEMPLATE_DIR . self::DEFAULT_MIGRATION_TEMPLATE;
    }

    /**
     * Returns the seed template filename.
     *
     * @return string
     */
    protected function getSeedTemplateFile()
    {
        return APP_SCAFFOLD_TEMPLATE_DIR . self::DEFAULT_SEED_TEMPLATE;
    }

    /**
     * 获取 application 数据配置文件目录
     * @return string
     */
    protected function getApplicationDatabaseConfigPath()
    {
        return realpath(APP_PATH . 'database' . EXT);
    }

    protected function getApplicationDatabaseConfig($name = null, $defaultValue = null)
    {
        $config = require $this->getApplicationDatabaseConfigPath();
        if ($name) {
            return $config[$name] ?: $defaultValue;
        }
        return $config;
    }
}