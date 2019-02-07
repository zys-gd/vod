<?php

namespace App\Admin\Sonata;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;

/**
 * Class DeveloperAdmin
 */
class DeveloperAdmin extends AbstractAdmin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('uuid')
            ->add('name')
            ->add('email');
    }
}