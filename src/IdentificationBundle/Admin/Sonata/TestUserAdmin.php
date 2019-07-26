<?php

namespace IdentificationBundle\Admin\Sonata;

use App\Domain\Entity\Carrier;
use App\Domain\Repository\CarrierRepository;
use Doctrine\ORM\Query\Expr\Join;
use ExtrasBundle\Utils\UuidGenerator;
use IdentificationBundle\Entity\TestUser;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

/**
 * Class TestUserAdmin
 */
class TestUserAdmin extends AbstractAdmin
{
    /**
     * @return TestUser
     *
     * @throws \Exception
     */
    public function getNewInstance(): TestUser
    {
        return new TestUser(UuidGenerator::generate());
    }

    /**
     * @return array
     */
    public function getBatchActions()
    {
        return [];
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('userIdentifier')
            ->add('carrier', null, [], EntityType::class, [
                'class' => Carrier::class,
                'query_builder' => function (CarrierRepository $carrierRepository) {
                    return $carrierRepository
                        ->createQueryBuilder('c')
                        ->join(TestUser::class, 'tu', Join::WITH, 'c.uuid = tu.carrier')
                        ->where('tu.uuid IS NOT NULL');
                }
            ]);
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('uuid')
            ->add('carrier')
            ->add('userIdentifier')
            ->add('_action', null, [
                'actions' => [
                    'drop_user_data' => [
                        'template' => '@IdentificationAdmin/TestUser/drop_user_data_button.html.twig'
                    ],
                    'set_status_for_renew' => [
                        'template' => '@IdentificationAdmin/TestUser/set_status_for_renew_button.html.twig'
                    ]   ,
                    'clean_from_cross_subscription' => [
                        'template' => '@IdentificationAdmin/TestUser/clean_from_cross_subscription.html.twig'
                    ]
                ]
            ]);
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('userIdentifier')
            ->add('carrier', EntityType::class, [
                'class' => Carrier::class,
                'placeholder' => 'Select carrier'
            ]);
    }

    /**
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->clearExcept(['create', 'list']);

        $collection->add('drop_user_data', $this->getRouterIdParameter() . '/dropUserData');
        $collection->add('set_status_for_renew', $this->getRouterIdParameter() . '/setStatusForRenew');
        $collection->add('clean_from_cross_subscription', $this->getRouterIdParameter() . '/cleanFromCrossSubscription');

        parent::configureRoutes($collection);
    }
}