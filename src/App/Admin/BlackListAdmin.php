<?php

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use SubscriptionBundle\Entity\Subscription;
use Symfony\Component\HttpFoundation\Request;

class BlackListAdmin extends AbstractAdmin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('carrierId')
            ->add('alias')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('carrierId')
            ->add('alias')
            ->add('_action', null, array(
                'actions' => array(
                    'show' => array(),
                    'edit' => array(),
                    'delete' => array(),
                )
            ))
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('carrierId')
            ->add('alias')
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('carrierId')
            ->add('alias')
        ;
    }

    public function postPersist($blackList) {
        $doctrine = $this->getConfigurationPool()->getContainer()->get('Doctrine');
        if ($blackList && $blackList->getAlias()) {
            $billableUser = $doctrine->getRepository('UserBundle:BillableUser')->findOneBy(['identifier' => $blackList->getAlias()]);
            if ($billableUser) {
                $subscription = $doctrine->getRepository('SubscriptionBundleV2:Subscription')->findOneBy(['billableUser' => $billableUser]);
                if ($subscription && $subscription->getAction() != Subscription::ACTION_UNSUBSCRIBE) {
                    $subscriptionService = $this->getConfigurationPool()->getContainer()->get('subscription.subscription.service');
                    $subscriptionService->unsubscribe(new Request(['unsub_after_blacklisting' => 1]), $billableUser);
                }
            }
        }
    }

    public function postUpdate($blacklist) {
        $this->postPersist($blacklist);
    }
}
