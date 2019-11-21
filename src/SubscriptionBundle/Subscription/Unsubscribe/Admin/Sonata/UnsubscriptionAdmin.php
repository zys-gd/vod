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

    public function getDashboardActions()
    {
        $actions = [];

        $actions['import']['template'] = '@SubscriptionAdmin/Unsubscription/unsubscribe_form_button.html.twig';

        return $actions;
    }


    /**
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->clear();

        $collection->add('unsubscribe', 'unsubscribe');
        $collection->add('unsubscribe_form', 'unsubscribeForm');

        parent::configureRoutes($collection);
    }
}