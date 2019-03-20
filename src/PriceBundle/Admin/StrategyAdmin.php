<?php

namespace PriceBundle\Admin;

use App\Utils\UuidGenerator;
use PriceBundle\Entity\Strategy;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

/**
 * Class StrategyAdmin
 */
class StrategyAdmin extends AbstractAdmin
{
    /**
     * @return Strategy
     *
     * @throws \Exception
     */
    public function getNewInstance(): Strategy
    {
        return new Strategy(UuidGenerator::generate());
    }

    /**
     * @param DatagridMapper $datagridMapper
     *
     * @return void
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('uuid')
            ->add('name');
    }

    /**
     * @param ListMapper $listMapper
     *
     * @return void
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('uuid')
            ->add('name')
            ->add('bfStrategyId')
            ->add('_action', null, [
                'actions' => [
                    'edit' => [],
                    'delete' => [],
                ]
            ]);
    }

    /**
     * @param FormMapper $formMapper
     *
     * @return void
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name')
            ->add('bfStrategyId', IntegerType::class, [
                'required' => true
            ])
            ->end();
    }
}