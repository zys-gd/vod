<?php

namespace SubscriptionBundle\Admin\Sonata;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Route\RouteCollection;

/**
 * Class ComplaintsAdmin
 * @package SubscriptionBundle\Admin\Sonata
 */
class ComplaintsAdmin extends AbstractAdmin
{
    /**
     * @var string
     */
    protected $baseRoutePattern = 'complaints';

    /**
     * @param RouteCollection $collection
     */
    public function configureRoutes(RouteCollection $collection)
    {
        $collection->clearExcept(['create', 'list']);
        $collection->add('downloadCsv', 'downloadCsv');

        parent::configureRoutes($collection);
    }
}