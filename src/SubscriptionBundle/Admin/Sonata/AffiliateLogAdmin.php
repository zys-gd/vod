<?php

namespace SubscriptionBundle\Admin\Sonata;

use IdentificationBundle\Entity\TestUser;
use IdentificationBundle\Repository\TestUserRepository;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class AffiliateLogAdmin
 */
class AffiliateLogAdmin extends AbstractAdmin
{
    /**
     * @var TestUserRepository
     */
    private $testUserRepository;

    /**
     * @var array
     */
    protected $datagridValues = array(
        '_sort_order' => 'DESC',
        '_sort_by'    => 'addedAt'
    );

    /**
     * AffiliateLogAdmin constructor
     *
     * @param string $code
     * @param string $class
     * @param string $baseControllerName
     * @param TestUserRepository $testUserRepository
     */
    public function __construct(
        string $code,
        string $class,
        string $baseControllerName,
        TestUserRepository $testUserRepository
    ) {
        $this->testUserRepository = $testUserRepository;

        parent::__construct($code, $class, $baseControllerName);
    }

    /**
     * @param string $context
     *
     * @return ProxyQueryInterface
     */
    public function createQuery($context = 'list')
    {
        $testUsersMsisdns = array_map(function (TestUser $testUser) {
            return $testUser->getUserIdentifier();
        }, $this->testUserRepository->findAll());

        $query = parent::createQuery($context);

        $query->andWhere($query->expr()->in('o.userMsisdn', $testUsersMsisdns));

        return $query;
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
        $testUsers = $this->testUserRepository->findAll();

        $choices = [];

        /** @var TestUser $testUser */
        foreach ($testUsers as $testUser) {
            $label = sprintf('%s (%s)', $testUser->getUserIdentifier(), $testUser->getCarrier()->getName());
            $choices[$label] = $testUser->getUserIdentifier();
        }

        $datagridMapper->add('userMsisdn', null, [], ChoiceType::class, ['choices' => $choices]);
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('uuid')
            ->add('userMsisdn', TextType::class, [
                'label' => 'User Identifier'
            ])
            ->add('addedAt')
            ->add('url')
            ->add('campaignToken');
    }

    /**
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->clearExcept(['list']);

        parent::configureRoutes($collection);
    }
}