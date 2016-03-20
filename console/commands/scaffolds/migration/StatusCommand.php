<?php namespace console\commands\scaffolds\migration;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/19
 * Time: 0:29
 */

use think\console\input\Option;
use think\console\Input;
use think\console\Output;

class StatusCommand extends AbstractMigrationCommand
{

    public function __construct()
    {
        parent::__construct('migrate:status');
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();

        $this->addOption('--environment', '-e', Option::VALUE_REQUIRED, 'The target environment.');

        $this->setName('migrate:status')
            ->setDescription('Show migration status')
            ->addOption('--format', '-f', Option::VALUE_REQUIRED, 'The output format: text or json. Defaults to text.')
            ->setHelp(
                <<<EOT
                The <info>status</info> command prints a list of all migrations, along with their current status

<info>phinx status -e development</info>
<info>phinx status -e development -f json</info>
EOT
            );
    }

    /**
     * Show the migration status.
     *
     * @param Input $input
     * @param Output $output
     * @return integer 0 if all migrations are up, or an error code
     */
    protected function execute(Input $input, Output $output)
    {
        $this->bootstrap($input, $output);

        $environment = $input->getOption('environment');
        $format = $input->getOption('format');

        if (null === $environment) {
            $environment = $this->getConfig()->getDefaultEnvironment();
            $output->writeln('<comment>warning</comment> no environment specified, defaulting to: ' . $environment);
        } else {
            $output->writeln('<info>using environment</info> ' . $environment);
        }
        if (null !== $format) {
            $output->writeln('<info>using format</info> ' . $format);
        }

        // print the status
        return $this->getManager()->printStatus($environment, $format);
    }
}