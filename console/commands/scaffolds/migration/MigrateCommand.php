<?php namespace console\commands\scaffolds\migration;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/18
 * Time: 0:36
 */
use think\console\Input;
use think\console\input\Option;
use think\console\Output;


class MigrateCommand extends AbstractMigrationCommand
{

    public function __construct()
    {
        parent::__construct('migrate');
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();

        $this->addOption('--environment', '-e', Option::VALUE_REQUIRED, 'The target environment');

        $this->setName('migrate')
            ->setDescription('Migrate the database')
            ->addOption('--target', '-t', Option::VALUE_REQUIRED, 'The version number to migrate to')
            ->addOption('--date', '-d', Option::VALUE_REQUIRED, 'The date to migrate to')
            ->setHelp(
                <<<EOT
                The <info>migrate</info> command runs all available migrations, optionally up to a specific version

<info>phinx migrate -e development</info>
<info>phinx migrate -e development -t 20110103081132</info>
<info>phinx migrate -e development -d 20110103</info>
<info>phinx migrate -e development -v</info>

EOT
            );
    }

    /**
     * Migrate the database.
     *
     * @param Input $input
     * @param Output $output
     * @return void
     */
    protected function execute(Input $input, Output $output)
    {
        $this->bootstrap($input, $output);

        $version     = $input->getOption('target');
        $environment = $input->getOption('environment');
        $date        = $input->getOption('date');

        if (null === $environment) {
            $environment = $this->getConfig()->getDefaultEnvironment();
            $output->writeln('<comment>warning</comment> no environment specified, defaulting to: ' . $environment);
        } else {
            $output->writeln('<info>using environment</info> ' . $environment);
        }

        $envOptions = $this->getConfig()->getEnvironment($environment);
        if (isset($envOptions['adapter'])) {
            $output->writeln('<info>using adapter</info> ' . $envOptions['adapter']);
        }

        if (isset($envOptions['wrapper'])) {
            $output->writeln('<info>using wrapper</info> ' . $envOptions['wrapper']);
        }

        if (isset($envOptions['name'])) {
            $output->writeln('<info>using database</info> ' . $envOptions['name']);
        } else {
            $output->writeln('<error>Could not determine database name! Please specify a database name in your config file.</error>');
            return;
        }

        if (isset($envOptions['table_prefix'])) {
            $output->writeln('<info>using table prefix</info> ' . $envOptions['table_prefix']);
        }
        if (isset($envOptions['table_suffix'])) {
            $output->writeln('<info>using table suffix</info> ' . $envOptions['table_suffix']);
        }

        // run the migrations
        $start = microtime(true);
        if (null !== $date) {
            $this->getManager()->migrateToDateTime($environment, new \DateTime($date));
        } else {
            $this->getManager()->migrate($environment, $version);
        }
        $end = microtime(true);

        $output->writeln('');
        $output->writeln('<comment>All Done. Took ' . sprintf('%.4fs', $end - $start) . '</comment>');
    }
}