<?php

namespace SubscriptionBundle\Admin\Sonata;

use App\Utils\UuidGenerator;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use SubscriptionBundle\Entity\Unsubscription;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class Unsubscription
 */
class UnsubscriptionAdmin extends AbstractAdmin
{
    /**
     * @return Unsubscription
     *
     * @throws \Exception
     */
    public function getNewInstance()
    {
        return new Unsubscription(UuidGenerator::generate());
    }

    /**
     * @param DatagridMapper $filter
     */
    protected function configureDatagridFilters(DatagridMapper $filter)
    {
        $filter
            ->add('type')
            ->add('subscription')
            ->add('status');
    }

    /**
     * @param ListMapper $list
     */
    protected function configureListFields(ListMapper $list)
    {
        $list
            ->add('type')
            ->add('subscription')
            ->add('unsubscribeDate')
            ->add('status');
    }

    /**
     * @param ShowMapper $show
     */
    protected function configureShowFields(ShowMapper $show)
    {
        $show
            ->add('uuid')
            ->add('type')
            ->add('subscription')
            ->add('unsubscribeDate')
            ->add('status');
    }

    protected function configureFormFields(FormMapper $form)
    {
        $this->buildMsisdnTab($form);
    }

    private function buildMsisdnTab(FormMapper $form)
    {
        $form
            ->tab('MSISDN')
            ->with('', ['box_class' => 'box-solid'])
            ->add('msisdn', TextType::class, [
                'mapped' => false
            ])
            ->end();
    }

    /**
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->clearExcept(['create', 'show', 'list']);

        parent::configureRoutes($collection);
    }
}