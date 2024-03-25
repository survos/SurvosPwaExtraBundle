<?php

// use https://www.rebasedata.com/upload?outputFormat=sqlite&referer=/convert-mysql-to-sqlite-online to convert from downloaded mysql sql zip file
namespace Survos\PwaExtraBundle\Command;

use Survos\PhenxBundle\Services\ImportService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[AsCommand(
    name: 'pwa:configure',
    description: 'create pwa.yaml from composer.json, etc.',
)]
class PwaConfigureCommand extends Command
{

    public function __construct(private ParameterBagInterface $bag)
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->addArgument('name', InputArgument::OPTIONAL, 'application name', null)
            ->addOption('force', null, InputOption::VALUE_NONE, 'Override')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // get some defaults, like composer.json keys
        $projectDir = $this->bag->get('kernel.project_dir');
        $composerFilename = $projectDir . '/composer.json';
        if (!file_exists($composerFilename)) {
            $output->writeln("Missing composer.json!");
            return self::FAILURE;
        }
        $composerData = json_decode(file_get_contents($composerFilename), true);
        foreach (['name','description'] as $requiredField) {
            if (!array_key_exists($requiredField, $composerData)) {
                $output->writeln("Missing $requiredField in composer.json, please add it with ");
                $output->writeln("composer config $requiredField ");
                return self::FAILURE;
            }
        }

        $output->writeln("<info>Done</info>");
        return self::SUCCESS;
    }
}
