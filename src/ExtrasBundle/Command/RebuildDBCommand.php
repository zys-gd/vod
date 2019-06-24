<?php


namespace ExtrasBundle\Command;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RebuildDBCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('doctrine:schema:rebuild')
            ->setDescription('Rebuild schema.')
            ->setHelp('Drop existing schema. Create a new one. Load fixtures. Add migrations.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getApplication()->find('doctrine:schema:drop')
            ->run( new ArrayInput(['--force' => true]), $output);

        $this->getApplication()->find('doctrine:schema:create')
            ->run( new ArrayInput([]), $output);

        $this->getApplication()->find('doctrine:fixtures:load')
            ->run( new ArrayInput(['--no-interaction' => true]), $output);

        $this->getApplication()->find('doctrine:migrations:version')
            ->run( new ArrayInput(['--add' => true, '--all' => true, '--no-interaction' => true]), $output);
    }
}