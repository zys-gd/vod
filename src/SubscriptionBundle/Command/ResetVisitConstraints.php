<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 14.05.19
 * Time: 13:20
 */

namespace SubscriptionBundle\Command;


use SubscriptionBundle\Affiliate\CapConstraint\VisitStorage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ResetVisitConstraints extends Command
{
    /**
     * @var VisitStorage
     */
    private $visitStorage;


    /**
     * ResetVisitConstraints constructor.
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