<?php

// use https://www.rebasedata.com/upload?outputFormat=sqlite&referer=/convert-mysql-to-sqlite-online to convert from downloaded mysql sql zip file
namespace Survos\PwaExtraBundle\Command;

use Survos\PhenxBundle\Services\ImportService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[AsCommand(
    name: 'pwa:configure',
    description: 'create pwa.yaml from composer.json, etc.',
)]
class ConfigureCommand extends Command
{

    public function __construct(private ParameterBagInterface $bag)
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->addOption('scrape', null, InputOption::VALUE_NONE, 'If set, scrape')
            ->addOption('process', null, InputOption::VALUE_NONE, 'If set, process whatever has been scraped')
            ->addOption('table', null, InputOption::VALUE_OPTIONAL, 'Which Table')
            ->addOption('limit', null, InputOption::VALUE_OPTIONAL, 'Number of records', 20);

        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // get some defaults, like composer.json keys
        $projectDir = $this->bag->get('kernel.project_dir');

        // https://www.svgrepo.com/vectors/silly
        // ice-cream-stick-svgrepo-com.svg

        /** @var $service ImportService */
        $service = $this->service;

        if ($table = $input->getOption('table')) {
            $tables = array($table);
        } else {
            $tables = array('Measure','Protocol');
        }

        if (!$input->getOption('scrape') && !$input->getOption('process')) {
            die("Specify either --scrape or --process\n");
        }

        foreach ($tables as $table) {
            $output->writeln("<info>$table</info>");

            if ($input->getOption('scrape')) {
                $output->writeln("<info>Scraping $table</info>");
                $service->scrape($table, $input->getOption('limit'));
            }

            if ($input->getOption('process')) {
                $output->writeln("<info>Processing $table</info>");
                switch ($table) {
                    case 'Protocol':
                        $service->processProtocol($input->getOption('limit'));
                        break;
                    case 'Measure':
                        $service->processMeasure($input->getOption('limit'));
                        break;
                }
            }
            $output->writeln("<info>Done</info>");
        }
        return self::SUCCESS;
    }
}
