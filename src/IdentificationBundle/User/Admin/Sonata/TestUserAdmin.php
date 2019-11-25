<?php

namespace IdentificationBundle\User\Admin\Sonata;

use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use ExtrasBundle\Utils\RealClassnameResolver;
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
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(string $code, string $class, string $baseControllerName, EntityManagerInterface $entityManager)
    {
        parent::__construct($code, $class, $baseControllerName);
        $this->entityManager = $entityManager;
    }


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
                'class'         => RealClassnameResolver::resolveName(CarrierInterface::class, $this->entityManager),
                'query_builder' => function (EntityRepository $carrierRepository) {
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
                    'drop_user_data'                => [
                        'template' => '@IdentificationAdmin/TestUser/drop_user_data_button.html.twig'
                    ],
                    'set_status_for_renew'          => [
                        'template' => '@IdentificationAdmin/TestUser/set_status_for_renew_button.html.twig'
                    ],
                    'clean_from_cross_subscription' => [
                        'template' => '@IdentificationAdmin/TestUser/clean_from_cross_subscription.html.twig'
                    ],
                    'delete'                        => []
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
                'class'       => RealClassnameResolver::resolveName(CarrierInterface::class, $this->entityManager),
                'placeholder' => 'Select carrier'
            ]);
    }

    /**
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->clearExcept(['create', 'list', 'delete']);

        $collection->add('drop_user_data', $this->getRouterIdParameter() . '/dropUserData');
        $collection->add('set_status_for_renew', $this->getRouterIdParameter() . '/setStatusForRenew');
        $collection->add('clean_from_cross_subscription', $this->getRouterIdParameter() . '/cleanFromCrossSubscription');

        parent::configureRoutes($collection);
    }
}