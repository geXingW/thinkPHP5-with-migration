<?php namespace console\commands\scaffolds\migration;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/17
 * Time: 22:50
 */
use think\phinx\util\Util;
use think\console\input\Argument;
use think\console\Input;
use think\console\Output;
use think\console\helper\question\Confirmation;

class MakeSeederCommand extends AbstractMigrationCommand
{
    public function __construct()
    {
        parent::__construct('make:seeder');
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();

        $this->setName('make:seeder')
            ->setDescription('Create a new database seeder')
            ->addArgument('name', Argument::REQUIRED, 'What is the name of the seeder?')
            ->setHelp(sprintf(
                '%sCreates a new database seeder%s',
                PHP_EOL,
                PHP_EOL
            ));
    }

    /**
     * Get the confirmation question asking if the user wants to create the
     * seeds directory.
     *
     * @return Confirmation
     */
    protected function getCreateSeedDirectoryQuestion()
    {
        return new Confirmation('Create seeds directory? [y]/n ', true);
    }

    /**
     * Create the new seeder.
     *
     * @param Input $input
     * @param Output $output
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     * @return void
     */
    protected function execute(Input $input, Output $output)
    {
        $this->bootstrap($input, $output);

        // get the seed path from the config
        $path = $this->getConfig()->getSeedPath();

        if (!file_exists($path)) {
            $helper = $this->getHelper('question');
            $question = $this->getCreateSeedDirectoryQuestion();

            if ($helper->ask($input, $output, $question)) {
                mkdir($path, 0755, true);
            }
        }

        $this->verifySeedDirectory($path);

        $path = realpath($path);
        $className = $input->getArgument('name');

        if (!Util::isValidPhinxClassName($className)) {
            throw new \InvalidArgumentException(sprintf(
                'The seed class name "%s" is invalid. Please use CamelCase format',
                $className
            ));
        }

        // Compute the file path
        $filePath = $path . DIRECTORY_SEPARATOR . $className . '.php';

        if (is_file($filePath)) {
            throw new \InvalidArgumentException(sprintf(
                'The file "%s" already exists',
                basename($filePath)
            ));
        }

        $altTemplate = $this->getConfig()->getSeederTemplate();

        // inject the class names appropriate to this seeder
        $contents = file_get_contents($altTemplate ?: $this->getSeedTemplateFile());
        $classes = array(
            '$useClassName' => 'think\phinx\seed\AbstractSeed',
            '$className' => $className,
            '$baseClassName' => 'AbstractSeed',
        );
        $contents = strtr($contents, $classes);

        if (false === file_put_contents($filePath, $contents)) {
            throw new \RuntimeException(sprintf(
                'The file "%s" could not be written to',
                $path
            ));
        }

        $output->writeln('<info>using seed base class</info> ' . $classes['$useClassName']);
        $output->writeln('<info>created</info> .' . str_replace(getcwd(), '', $filePath));
    }
}