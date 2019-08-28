<?php

namespace SubscriptionBundle\CAPTool\Visit\Command;

use SubscriptionBundle\CAPTool\Visit\VisitStorage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ResetVisitConstraints
 */
class ResetVisitConstraints extends Command
{
    /**
     * @var VisitStorage
     */
    private $visitStorage;

    /**
     * ResetVisitConstraints constructor
     *
     * @param VisitStorage $visitStorage
     */
    public function __construct(VisitStorage $visitStorage)
    {
        $this->visitStorage = $visitStorage;

        parent::__construct();
    }

    public function configure()
    {
        $this->setName('cap:visit-counter:reset');
        $this->setHelp('Reset from redis all counters for constraints by affiliate');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->visitStorage->cleanVisits();
    }
}