<?php

namespace SubscriptionBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use SubscriptionBundle\Repository\SubscriptionRepository;


class AlertCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('send:alert')
            ->setDescription('Sends alerts about a big count of pending subscriptions')
            ->setHelp('This command checks the count of pending subscriptions and sends alert if the count of pending subscription is more than the defined value');
    }

    protected function execute( InputInterface $input, OutputInterface $output)
    {
        $pendingLimit = 2; // limit for pending subscription before sending alert
        $container = $this->getContainer();
        $entityManager = $container->get('doctrine.orm.entity_manager');
        $entityManager->getConnection()->getConfiguration()->setSQLLogger(null);
        $logger = $container->get('logger');
        /** @var SubscriptionRepository $subscriptionRepo */
        $subscriptionRepo = $container->get('subscription.subscription.repository');
        $pendingSubCount = $subscriptionRepo->findPendingSubscription();
        if ($pendingSubCount > $pendingLimit) {
            $message = (new \Swift_Message('Alert pending subsriptions'))
                ->setFrom('support.form@playwing.net')
                ->setTo('ganzaevshow@gmail.com')
                ->setBody(
                    "Alert! " .$pendingSubCount ." pending subscriptions!"
                );
            $container->get('mailer')->send($message);
            $output->writeln('Mail send');
        }

                $logger->info("Alert! " . $pendingSubCount . "pending subscriptions");
        $output->writeln('Command executed');
    }
}
