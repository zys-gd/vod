<?php

namespace SubscriptionBundle\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateGamesToSubscriptionV2Command extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('user:subscription:migrate-games')
            ->setDescription('Migrate older subscriptions to new subscription');
        $this->addArgument(
            'id1',
            InputArgument::REQUIRED,
            "Migrates subscriptions for the provided billable user id",
            null
        );
        $this->addArgument('id2',
            InputArgument::REQUIRED,
            "Migrates subscriptions for the provided billable user id",
            null);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $a         = 0;
        $idRange   = $input->getArgument('id1');
        $idRange2  = $input->getArgument('id2');
        $container = $this->getContainer();
        /** @var EntityManager $entityManager */
        $entityManager = $container->get('doctrine.orm.entity_manager');
        do {
            $q  = "
      INSERT INTO subscribed_games (game_id, subscription_id, first_download, last_download) 
        SELECT 
            g.game_id AS 'game_id', 
            s.id AS 'subscription_id', 
            g.first_download_date AS 'first_download',
            g.first_download_date AS 'last_download'
        FROM game_downloads AS g 
        INNER JOIN subscriptionv2 AS s ON g.billable_user_id = s.owner_id
        WHERE s.id BETWEEN $idRange AND $idRange2 LIMIT 500 OFFSET $a";
            $qb = $entityManager->getConnection()->prepare($q);
            $qb->execute();
            $count = $qb->rowCount();
            echo $a += $count;
            echo PHP_EOL;
        } while ($count == 500);

    }
}