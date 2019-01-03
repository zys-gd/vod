<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 5/14/2018
 * Time: 10:32 AM
 */

namespace App\Admin\Sonata;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Doctrine\ORM\EntityRepository;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;

class RefundAdmin extends AbstractAdmin
{
    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('billableUser.identifier')
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
            ->add('id')
            ->add('billableUser.identifier')
            ->add('status')
            ->add('error');
    }
}
