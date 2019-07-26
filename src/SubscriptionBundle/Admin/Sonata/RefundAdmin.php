<?php

namespace SubscriptionBundle\Admin\Sonata;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;

/**
 * Class RefundAdmin
 */
class RefundAdmin extends AbstractAdmin
{
    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('user.identifier')
            ->add('status')
            ->add('error')
            ->add('bf_charge_process_id')
            ->add('bf_refund_process_id')
            ->add('attemptDate')
            ->add('refund_value');
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('user.identifier')
            ->add('status')
            ->add('error');
    }
}
