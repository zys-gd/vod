<?php

namespace SubscriptionBundle;

use SubscriptionBundle\DependencyInjection\Compiler\CallbackHandlerPass;
use SubscriptionBundle\DependencyInjection\Compiler\CampaignConfirmationPass;
use SubscriptionBundle\DependencyInjection\Compiler\NotificationHandlerPass;
use SubscriptionBundle\DependencyInjection\Compiler\RenewHandlerPass;
use SubscriptionBundle\DependencyInjection\Compiler\SMSTextHandlerPass;
use SubscriptionBundle\DependencyInjection\Compiler\SubscribeBackHandlerPass;
use SubscriptionBundle\DependencyInjection\Compiler\SubscriptionHandlerPass;
use SubscriptionBundle\DependencyInjection\Compiler\SubscriptionVoterPass;
use SubscriptionBundle\DependencyInjection\Compiler\TwigAdditionalPathsExtension;
use SubscriptionBundle\DependencyInjection\Compiler\UnsubscriptionHandlerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SubscriptionBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new SubscriptionHandlerPass());
        $container->addCompilerPass(new CallbackHandlerPass());
        $container->addCompilerPass(new UnsubscriptionHandlerPass());
        $container->addCompilerPass(new NotificationHandlerPass());
        $container->addCompilerPass(new RenewHandlerPass());
        $container->addCompilerPass(new CampaignConfirmationPass());
        $container->addCompilerPass(new SubscriptionVoterPass());

        $container->addCompilerPass(new TwigAdditionalPathsExtension());
        $container->addCompilerPass(new SMSTextHandlerPass());
        $container->addCompilerPass(new SubscribeBackHandlerPass());
    }
}
