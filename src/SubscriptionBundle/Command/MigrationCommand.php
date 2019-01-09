<?php

namespace SubscriptionBundle\Command;


use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\VarDumper\VarDumper;

class MigrationCommand extends ContainerAwareCommand
{
    public function configure()
    {
        $this->setName('user:subscription:migration');
        $this->setHelp('This command should migrate users.');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $a         = 0;
        /** @var EntityManager $entityManager */
        $entityManager = $container->get('doctrine.orm.entity_manager');
        $connection    = $entityManager->getConnection();

        $statement = $connection->prepare('SET FOREIGN_KEY_CHECKS = 0;TRUNCATE subscriptionv2');
        $statement->execute();
        $statement->closeCursor();


        try {


            $connection->beginTransaction();

            $q = "
                      INSERT INTO subscriptionv2 (
                        id,
                        owner_id,
                        created, 
                        updated,
                        renew_date, 
                        subscription_pack_id, 
                        affiliate_token, 
                        current_stage, 
                        status, 
                        credits, 
                        error
                      )
                      SELECT
                        s.id as 'id',
                        s.billable_user_id as 'owner_id',
                        s.added_at as 'created',
                        s.updated_at as 'updated',
                        s.expired_at as 'renew_date',
                        sp.id as 'subscription_pack_id',
                        s.affiliate_token as 'affiliate_token',
                        s.action AS 'current_stage',
                        s.status as 'status' ,
                        sp.credits as 'credits',
                        s.error AS 'error'
                      FROM subscriptions as s
                        JOIN billable_users AS bu ON s.billable_user_id = bu.id
                        JOIN carriers AS car ON car.id = bu.carrier_id
                        JOIN subscription_pack As sp ON sp.id = (
                          select id from subscription_pack
                          where car.id_carrier = subscription_pack.carrier_id
                          limit 1
                        );
                    ";

            $statement = $connection->prepare($q);
            $statement->execute();

            $count = $statement->rowCount();
            $statement->closeCursor();

            $connection->commit();

            echo $a += $count;
            echo PHP_EOL;


        } catch (\Exception $e) {
            $connection->rollBack();
            echo $e->getMessage();
        }
    }


}