<?php

namespace SubscriptionBundle\Subscription\Unsubscribe\Admin\Sonata;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Route\RouteCollection;

/**
 * Class Unsubscription
 */
class UnsubscriptionAdmin extends AbstractAdmin
{
    /**
     * @var string
     */
    protected $baseRoutePattern = 'unsubscription';

    /**
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->clearExcept(['create', 'list']);
        $collection->add('unsubscribe', 'unsubscribe');

        parent::configureRoutes($collection);
    }
}