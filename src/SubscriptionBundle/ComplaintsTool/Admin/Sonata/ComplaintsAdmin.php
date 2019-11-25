<?php

namespace SubscriptionBundle\ComplaintsTool\Admin\Sonata;

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

    public function getDashboardActions()
    {

        $actions = [];

        $actions['report']['template'] = '@SubscriptionAdmin/Complaints/make_report_button.html.twig';

        return $actions;

    }


    /**
     * @param RouteCollection $collection
     */
    public function configureRoutes(RouteCollection $collection)
    {
        $collection->clear();

        $collection->add('report', 'report');
        $collection->add('downloadCsv', 'downloadCsv');
        $collection->add('downloadExcel', 'downloadExcel');

        parent::configureRoutes($collection);
    }
}