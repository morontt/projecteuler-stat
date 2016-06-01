<?php
/**
 * Created by PhpStorm.
 * User: morontt
 * Date: 02.06.16
 * Time: 22:09
 */

namespace MttProjecteuler\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DbSchema extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('db:schema')
            ->setDescription('Create schema database')
            ->addOption('dump-sql', null, InputOption::VALUE_NONE, 'Dumps the generated SQL (does not execute them)')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $queries = $this->container['pe_database.migrator']->migrate($input->getOption('dump-sql'));
        foreach ($queries as $query) {
            $output->writeln($query);
        }

        if (count($queries)) {
            if ($input->getOption('dump-sql')) {
                $output->writeln('<info>dump SQL schema</info>');
            } else {
                $output->writeln('<info>DB schema updated</info>');
            }
        } else {
            $output->writeln('<info>Nothing to update</info>');
        }
    }
}
