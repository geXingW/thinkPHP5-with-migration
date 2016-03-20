<?php namespace console\commands\scaffolds\migration;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/19
 * Time: 0:03
 */

use think\console\Input;
use think\console\Output;
use think\console\input\Option;

class RollBackCommand extends AbstractMigrationCommand
{

    public function __construct()
    {
        parent::__construct('migrate:rollback');
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();

        $this->addOption('--environment', '-e', Option::VALUE_REQUIRED, 'The target environment');

        $this->setName('migrate:rollback')
            ->setDescription('Rollback the last or to a specific migration')
            ->addOption('--target', '-t', Option::VALUE_REQUIRED, 'The version number to rollback to')
            ->addOption('--date', '-d', Option::VALUE_REQUIRED, 'The date to rollback to')
            ->setHelp(
                <<<EOT
                The <info>rollback</info> command reverts the last migration, or optionally up to a specific version

<info>phinx rollback -e development</info>
<info>phinx rollback -e development -t 20111018185412</info>
<info>phinx rollback -e development -d 20111018</info>
<info>phinx rollback -e development -v</info>

EOT
            );
    }

    /**
     * Rollback the migration.
     *
     * @param Input $input
     * @param Output $output
     * @return void
     */
    protected function execute(Input $input, Output $output)
    {
        $this->bootstrap($input, $output);

        $environment = $input->getOption('environment');
        $version     = $input->getOption('target');
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
        }

        // rollback the specified environment
        $start = microtime(true);
        if (null !== $date) {
            $this->getManager()->rollbackToDateTime($environment, new \DateTime($date));
        } else {
            $this->getManager()->rollback($environment, $version);
        }
        $end = microtime(true);

        $output->writeln('');
        $output->writeln('<comment>All Done. Took ' . sprintf('%.4fs', $end - $start) . '</comment>');
    }
}