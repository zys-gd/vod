<?php

namespace SubscriptionBundle\SubscriptionPack\Admin\Sonata;

use CommonDataBundle\Entity\Country;
use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use CommonDataBundle\Repository\CountryRepository;
use Doctrine\ORM\EntityManagerInterface;
use ExtrasBundle\Utils\RealClassnameResolver;
use ExtrasBundle\Utils\UuidGenerator;
use IdentificationBundle\Repository\CarrierRepositoryInterface;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ChoiceFieldMaskType;
use Sonata\AdminBundle\Route\RouteCollection;
use SubscriptionBundle\BillingFramework\Process\Exception\BillingFrameworkException;
use SubscriptionBundle\BillingFramework\Process\SubscriptionPackDataProvider;
use SubscriptionBundle\Entity\SubscriptionPack;
use SubscriptionBundle\Repository\SubscriptionPackRepository;
use SubscriptionBundle\SubscriptionPack\DTO\Strategy;
use SubscriptionBundle\SubscriptionPack\DTO\Tier;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;

/**
 * Class SubscriptionPackAdmin
 */
class SubscriptionPackAdmin extends AbstractAdmin
{

    /**
     * @var SubscriptionPackDataProvider
     */
    private $subscriptionPackDataProvider;

    /**
     * @var CarrierRepositoryInterface
     */
    private $carrierRepository;
    /**
     * @var SubscriptionPackRepository
     */
    private $subscriptionPackRepository;
    /**
     * @var CountryRepository
     */
    private $countryRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @param string                       $code
     * @param string                       $class
     * @param string                       $baseControllerName
     * @param SubscriptionPackDataProvider $subscriptionPackDataProvider
     * @param CarrierRepositoryInterface   $carrierRepository
     * @param SubscriptionPackRepository   $subscriptionPackRepository
     * @param CountryRepository            $countryRepository
     * @param EntityManagerInterface       $entityManager
     */
    public function __construct(
        $code,
        $class,
        $baseControllerName,
        SubscriptionPackDataProvider $subscriptionPackDataProvider,
        CarrierRepositoryInterface $carrierRepository,
        SubscriptionPackRepository $subscriptionPackRepository,
        CountryRepository $countryRepository,
        EntityManagerInterface $entityManager

    )
    {
        parent::__construct($code, $class, $baseControllerName);
        $this->subscriptionPackDataProvider = $subscriptionPackDataProvider;
        $this->carrierRepository            = $carrierRepository;
        $this->subscriptionPackRepository   = $subscriptionPackRepository;
        $this->countryRepository            = $countryRepository;
        $this->entityManager                = $entityManager;
    }

    /**
     * @return SubscriptionPack
     * @throws \Exception
     */
    public function getNewInstance(): SubscriptionPack
    {
        $dateTimeNow = new \DateTime('now');

        /** @var SubscriptionPack $instance */
        $instance = new SubscriptionPack(UuidGenerator::generate());
        $instance->setCustomRenewPeriod(0);
        $instance->setUnlimited(false);
        $instance->setCredits(0);
        $instance->setCreated($dateTimeNow);
        $instance->setUpdated($dateTimeNow);

        return $instance;
    }

    /**
     * @param SubscriptionPack $object
     *
     * @throws \Exception
     */
    public function preUpdate($object)
    {
        $object->setUpdated(new \DateTime('now'));
        // resolve problems with form save and inline list save
        try {
            $object->setBuyStrategyId($object->getBuyStrategyId()->id);
            $object->setRenewStrategyId($object->getRenewStrategyId()->id);
            $object->setTierId($object->getTierId()->id);
        } catch (\Throwable $e) {
            // then save by default behavior
        }

        $this->markSubscriptionPacksWithSameCarrierAsInactive($object);

        parent::preUpdate($object);
    }

    /**
     * @param SubscriptionPack $object
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function prePersist($object)
    {
        $object->setBuyStrategyId($object->getBuyStrategyId()->id);
        $object->setRenewStrategyId($object->getRenewStrategyId()->id);
        $this->markSubscriptionPacksWithSameCarrierAsInactive($object);
    }

    /**
     * @param string $action
     * @param null   $object
     *
     * @return array
     */
    public function getActionButtons($action, $object = null): array
    {
        return array_merge(
            parent::getActionButtons($action, $object),
            ['template' => '@SubscriptionBundle/SubscriptionPack/go-to-texts-button.html.twig']
        );
    }

    /**
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->add('success', $this->getRouterIdParameter() . '/success');
        $collection->add('texts', $this->getRouterIdParameter() . '/texts');

        parent::configureRoutes($collection);
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name')
            ->add('country')
            ->add('carrier')
            ->add('unlimited', null, [
                'editable' => false,
                'label'    => 'Unlimited Downloads'
            ])
            ->add('credits', null, [
                'editable' => false,
                'label'    => 'Credits'
            ])
            ->add('periodicity', 'choice', [
                'editable' => true,
                'choices'  => array_flip(SubscriptionPack::PERIODICITY),
            ])
            ->add('zeroCreditSubAvailable')
            ->add('status', 'choice', [
                'editable' => true,
                'choices'  => array_flip(SubscriptionPack::STATUSES),
            ]);
    }

    /**
     * @param DatagridMapper $datagridMapper
     *
     * @throws BillingFrameworkException
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $tiers      = $this->subscriptionPackDataProvider->getTiers();
        $strategies = $this->subscriptionPackDataProvider->getBillingStrategies();

        $datagridMapper
            ->add('name')
            ->add('country')
            ->add('carrier')
            // ->add('carrierName', null, [], ChoiceType::class, [
            //     'choices'      => $carriers,
            //     'choice_label' => 'name',
            //     'choice_value' => 'id'
            // ])
            // ->add('buyStrategy', null, [], ChoiceType::class, [
            //     'choices'      => $strategies,
            //     'choice_label' => 'name',
            //     'choice_value' => 'id'
            // ])
            // ->add('renewStrategy', null, [], ChoiceType::class, [
            //     'choices'      => $strategies,
            //     'choice_label' => 'name',
            //     'choice_value' => 'id'
            // ])
            ->add('periodicity')
            // ->add('tier', null, [], ChoiceType::class, [
            //     'choices'      => $tiers,
            //     'choice_label' => 'name',
            //     'choice_value' => 'id'
            // ])
            ->add('status', null, [
                'label' => 'Subscription Pack Active'
            ])
            ->add('preferredRenewalStart', 'doctrine_orm_datetime_range')
            ->add('preferredRenewalEnd', 'doctrine_orm_datetime_range');
    }

    /**
     * @param FormMapper $formMapper
     *
     * @throws BillingFrameworkException
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $this->buildGeneralSection($formMapper);
        $this->buildBillingStrategySection($formMapper);
        $this->buildPromotionSections($formMapper);


    }

    /**
     * @param FormMapper $formMapper
     */
    private function buildGeneralSection(FormMapper $formMapper)
    {
        /** @var SubscriptionPack $subject */
        $subject = $this->getSubject();
        $country = $subject->getCountry();

        $formMapper
            ->add('name', TextType::class)
            ->add('description', TextareaType::class, [
                'required' => false
            ]);

        if ($country) {
            $formMapper
                ->add('country', EntityType::class, [
                    'attr'    => [
                        'readonly' => true,
                    ],
                    'choices' => [$country],
                    'class'   => Country::class
                ]);

            $formMapper
                ->add('carrier', EntityType::class, [
                    'attr'    => [
                        'readonly' => true,
                    ],
                    'choices' => [$subject->getCarrier()],
                    'class'   => RealClassnameResolver::resolveName(CarrierInterface::class, $this->entityManager),
                ]);
        } else {
            $formMapper->add('country', EntityType::class, [
                'class'        => Country::class,
                'label'        => 'Country',
                'expanded'     => false,
                'required'     => true,
                'placeholder'  => 'Please select country',
                'choices'      => $this->getCountryList(),
                'choice_label' => 'countryName',
                'choice_value' => 'uuid',
                'choice_attr'  => function ($data) {
                    return $data instanceof Country ?
                        ['data' => $this->getCountryCarriersAsJson($data)]
                        : $data;
                },
            ]);

            $formMapper->add('carrier', EntityType::class, [
                'class'       => RealClassnameResolver::resolveName(CarrierInterface::class, $this->entityManager),
                'label'       => 'Carrier',
                'expanded'    => false,
                'required'    => true,
                // 'choices'     => [],
                'placeholder' => 'Please select carrier'
            ]);
        }

        $formMapper
            ->add('tierPrice', TextType::class, [
                'required' => true
            ])
            ->add('tierCurrency', TextType::class, [
                'required' => true
            ])
            ->add('displayCurrency', TextType::class, [
                'required' => false,
                'label'    => 'Display currency symbol'
            ]);

        $formMapper
            ->add('periodicity', ChoiceFieldMaskType::class, [
                'choices'     => SubscriptionPack::PERIODICITY,
                'map'         => [
                    SubscriptionPack::CUSTOM_PERIODICITY => ['customRenewPeriod']
                ],
                'placeholder' => 'Please select periodicity',
                'required'    => true,
                'label'       => 'Periodicity'
            ])
            ->add('customRenewPeriod', IntegerType::class, [
                'required' => true,
                'label'    => 'No of subscribed days before auto renewal'
            ]);

        $formMapper
            ->add('unlimited', ChoiceFieldMaskType::class, [
                'choices'     => [
                    'Specify Credits' => 0,
                    'Unlimited'       => 1,
                ],
                'map'         => [
                    0 => ['credits'],
                ],
                'placeholder' => 'Please select credits',
                'required'    => true,
                'label'       => 'No of games to be downloaded'
            ])
            ->add('credits', IntegerType::class, [
                'required' => true,
                'label'    => 'No Of Games Can Be Downloaded in subscription period'
            ]);

        $formMapper
            ->add('unlimitedGracePeriod', ChoiceFieldMaskType::class, [
                'choices'     => [
                    'Specify Days' => 0,
                    'Infinite'     => 1,
                ],
                'map'         => [
                    0 => ['gracePeriod'],
                ],
                'placeholder' => 'Please select gace period',
                'required'    => true,
                'label'       => 'Credit expiration time',
                'help'        => 'A number of days that the user can download his credits after he is un-subscribed'
            ])
            ->add('gracePeriod', IntegerType::class);

        $formMapper
            ->add('preferredRenewalStart', TimeType::class, [
                'input'    => 'datetime',
                'required' => false,
                'widget'   => 'choice',
                'label'    => 'Preferred Renewal Start Time'
            ])
            ->add('preferredRenewalEnd', TimeType::class, [
                'input'    => 'datetime',
                'required' => false,
                'widget'   => 'choice',
                'label'    => 'Preferred Renewal End Time'
            ]);

        $formMapper
            ->add('welcomeSMSText', TextareaType::class, [
                'required' => false,
                'label'    => 'Welcome SMS Text'
            ])
            ->add('unsubscribeSMSText', TextareaType::class, [
                'required' => false,
                'label'    => 'Unsubscribe SMS Text'
            ])
            ->add('renewalSMSText', TextareaType::class, [
                'required' => false,
                'label'    => 'Renewal SMS Text'
            ]);

        $formMapper
            ->add('status', ChoiceType::class, [
                'choices' => SubscriptionPack::STATUSES,
                'label'   => 'Pack Status'
            ])
            ->end();
    }

    /**
     * @param FormMapper $formMapper
     *
     * @throws BillingFrameworkException
     */
    private function buildBillingStrategySection(FormMapper $formMapper)
    {
        $billingStrategies = $this->subscriptionPackDataProvider->getBillingStrategies();
        $billingTiers      = $this->subscriptionPackDataProvider->getTiers();

        $formMapper
            ->with('Billing strategy', [''])
            ->add('tierId', ChoiceType::class, [
                'label'        => 'Billing tier for subscription',
                'choices'      => $billingTiers,
                'choice_label' => 'name',
                'choice_value' => function ($billingTier) {
                    return $billingTier instanceof Tier ? $billingTier->id : $billingTier;
                }
            ]);

        $formMapper
            ->add('buyStrategyId', ChoiceType::class, [
                'label'        => 'Billing strategy for new subscription',
                'choices'      => $billingStrategies,
                'choice_label' => 'name',
                'choice_value' => function ($strategy) {
                    return $strategy instanceof Strategy ? $strategy->id : $strategy;
                }
            ]);

        $formMapper
            ->add('renewStrategyId', ChoiceType::class, [
                // 'label'        => 'Renew strategy',
                'choices'      => $billingStrategies,
                'choice_label' => 'name',
                'choice_value' => function ($strategy) {
                    return $strategy instanceof Strategy ? $strategy->id : $strategy;
                }
            ]);

        $formMapper
            ->add('providerManagedSubscriptions', CheckboxType::class, [
                'required' => false,
                'label'    => 'Subscriptions will be managed by provider'
            ])
            ->add('isResubAllowed', CheckboxType::class, [
                'required' => false,
                'label'    => 'Is resubscribe allowed'
            ]);

        $formMapper->add('zeroCreditSubAvailable');

        $formMapper->end();
    }

    /**
     * @param FormMapper $formMapper
     */
    private function buildPromotionSections(FormMapper $formMapper)
    {
        $formMapper
            ->with('Promotion 1', [''])
            ->add('firstSubscriptionPeriodIsFree', ChoiceFieldMaskType::class, [
                'choices'  => [
                    'Yes' => 1,
                    'No'  => 0
                ],
                'map'      => [
                    1 => ['firstSubscriptionPeriodIsFreeMultiple']
                ],
                'required' => true,
                'label'    => '1st subscription period is free (user will not be charged upon subscription)'
            ])
            ->add('firstSubscriptionPeriodIsFreeMultiple', CheckboxType::class, [
                'label'    => 'The user benefit from the same offer type more than once',
                'required' => false
            ])
            ->end();

        $formMapper
            ->with('Promotion 2')
            ->add('allowBonusCredit', ChoiceFieldMaskType::class, [
                'choices'  => [
                    'Yes' => 1,
                    'No'  => 0
                ],
                'map'      => [
                    1 => ['bonusCredit', 'allowBonusCreditMultiple']
                ],
                'required' => true,
                'label'    => 'Add bonus credit for first subscription period'
            ])
            ->add('bonusCredit', IntegerType::class, [
                'label'    => 'Please specify bonus credit',
                'required' => false
            ])
            ->add('allowBonusCreditMultiple', CheckboxType::class, [
                'label'    => 'The user benefit from the same offer type more than once',
                'required' => false
            ])
            ->end();
    }

    /**
     * @TODO Needs Yuri opinion
     * @param SubscriptionPack $subscriptionPack
     *
     */
    private function markSubscriptionPacksWithSameCarrierAsInactive(SubscriptionPack $subscriptionPack): void
    {
        if ($subscriptionPack->getStatus() == SubscriptionPack::ACTIVE_SUBSCRIPTION_PACK) {
            $subscriptionPacks = $this->subscriptionPackRepository->getActiveSubscriptionPacksByCarrierUuid($subscriptionPack);

            if (count($subscriptionPacks) > 0) {
                /** @var SubscriptionPack $subscriptionPack */
                foreach ($subscriptionPacks as $subscriptionPack) {
                    $subscriptionPack->setStatus(SubscriptionPack::INACTIVE_SUBSCRIPTION_PACK);
                    $this->entityManager->persist($subscriptionPack);
                }

                $this->entityManager->flush();
            }
        }
    }

    private function getCountryList()
    {
        $carrierInterfaces = $this->carrierRepository->findEnabledCarriers();

        $countriesCarriers = [];
        foreach ($carrierInterfaces as $carrier) {
            $countriesCarriers[] = $carrier->getCountryCode();
        }
        $countries = $this->countryRepository->findBy(['countryCode' => $countriesCarriers]);
        return $countries;
    }

    private function getCountryCarriersAsJson(Country $country)
    {
        $carrierInterfaces = $this->carrierRepository->findEnabledCarriers();

        $aCarriers = [];
        foreach ($carrierInterfaces as $carrier) {
            $carrierData = [
                'uuid'             => $carrier->getUuid(),
                'billingCarrierId' => $carrier->getBillingCarrierId(),
                'name'             => $carrier->getName(),
            ];
            $aCarriers[] = $carrierData;
        }
        return json_encode($aCarriers);
    }
}