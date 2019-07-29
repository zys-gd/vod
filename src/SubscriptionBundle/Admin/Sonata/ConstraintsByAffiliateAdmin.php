<?php

namespace SubscriptionBundle\Admin\Sonata;

use App\Domain\Entity\Affiliate;
use App\Domain\Entity\Carrier;
use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use Doctrine\ORM\EntityManagerInterface;
use ExtrasBundle\Utils\UuidGenerator;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use SubscriptionBundle\Admin\Service\ConstraintByAffiliateCapCalculator;
use SubscriptionBundle\CAPTool\DTO\AffiliateLimiterData;
use SubscriptionBundle\CAPTool\DTO\CarrierLimiterData;
use SubscriptionBundle\CAPTool\Subscription\Limiter\LimiterDataConverter;
use SubscriptionBundle\CAPTool\Subscription\Limiter\LimiterDataExtractor;
use SubscriptionBundle\Entity\Affiliate\ConstraintByAffiliate;
use SubscriptionBundle\Repository\Affiliate\ConstraintByAffiliateRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Class ConstraintsByAffiliateAdmin
 */
class ConstraintsByAffiliateAdmin extends AbstractAdmin
{
    /**
     * @var ConstraintByAffiliateRepository
     */
    private $constraintByAffiliateRepository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var ConstraintByAffiliateCapCalculator
     */
    private $affiliateCapCalculator;

    /**
     * ConstraintsByAffiliateAdmin constructor
     *
     * @param string                             $code
     * @param string                             $class
     * @param string                             $baseControllerName
     * @param ConstraintByAffiliateRepository    $constraintByAffiliateRepository
     * @param EntityManagerInterface             $entityManager
     * @param ConstraintByAffiliateCapCalculator $affiliateCapCalculator
     */
    public function __construct(
        string $code,
        string $class,
        string $baseControllerName,
        ConstraintByAffiliateRepository $constraintByAffiliateRepository,
        EntityManagerInterface $entityManager,
        ConstraintByAffiliateCapCalculator $affiliateCapCalculator
    )
    {
        $this->constraintByAffiliateRepository = $constraintByAffiliateRepository;
        $this->entityManager                   = $entityManager;
        $this->affiliateCapCalculator          = $affiliateCapCalculator;
        parent::__construct($code, $class, $baseControllerName);
    }

    /**
     * @return ConstraintByAffiliate
     * @throws \Exception
     */
    public function getNewInstance(): ConstraintByAffiliate
    {
        return new ConstraintByAffiliate(UuidGenerator::generate());
    }

    /**
     * @param ConstraintByAffiliate $object
     */
    public function preUpdate($object)
    {
        $originalData = $this->entityManager->getUnitOfWork()->getOriginalEntityData($object);

        if ($originalData['numberOfActions'] !== $object->getNumberOfActions()) {
            $object->setIsCapAlertDispatch(false);
        }
    }

    /**
     * @param CarrierInterface|null     $carrier
     * @param ExecutionContextInterface $context
     */
    public function validateForIdenticalRecord(?CarrierInterface $carrier, ExecutionContextInterface $context): void
    {
        /** @var ConstraintByAffiliate $constraintByAffiliate */
        $constraintByAffiliate = $this->getSubject();

        $affiliate = $constraintByAffiliate->getAffiliate();
        $capType   = $constraintByAffiliate->getCapType();

        $uuid = $this->constraintByAffiliateRepository->getIdenticalConstraintUuid($affiliate, $carrier, $capType);

        if ($uuid && $uuid !== $constraintByAffiliate->getUuid()) {
            $context
                ->buildViolation('Identical constraint for affiliate and carrier was found')
                ->addViolation();
        }
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('affiliate')
            ->add('carrier')
            ->add('capType');
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('affiliate')
            ->add('carrier')
            ->add('numberOfActions')
            ->add('capType')
            ->add('flushDate')
            ->add('isCapAlertDispatch', 'boolean', [
                'label' => 'Is email sent today'
            ])
            ->add('counter', 'string', ['template' => '@SubscriptionAdmin/ConstraintByAffiliate/counter.html.twig'])
            ->add('_action', null, [
                'actions' => [
                    'show'   => [],
                    'edit'   => [],
                    'delete' => [],
                ]
            ]);
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $isCreate = $this->isCurrentRoute('create');

        if ($isCreate) {
            $formMapper
                ->add('affiliate', EntityType::class, [
                    'class'       => Affiliate::class,
                    'placeholder' => 'Select affiliate',
                ])
                ->add('carrier', EntityType::class, [
                    'class'       => Carrier::class,
                    'constraints' => [
                        new Callback([$this, 'validateForIdenticalRecord'])
                    ],
                    'placeholder' => 'Select carrier',
                ])
                ->add('capType', ChoiceType::class, [
                    'choices' => [
                        'Subscribe' => ConstraintByAffiliate::CAP_TYPE_SUBSCRIBE,
                        'Visit'     => ConstraintByAffiliate::CAP_TYPE_VISIT
                    ],
                    'label'   => 'CAP type'
                ]);
        }

        $formMapper
            ->add('numberOfActions', IntegerType::class, [
                'attr'  => [
                    'min' => 0
                ],
                'label' => 'Number of allowed actions by constraint'
            ]);
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {

        /** @var ConstraintByAffiliate $subject */
        $subject = $this->getSubject();

        $used = $this->affiliateCapCalculator->calculateCounter($subject);

        $subject->setCounter($used);

        $showMapper
            ->add('affiliate')
            ->add('carrier')
            ->add('numberOfActions')
            ->add('counter')
            ->add('capType', TextType::class, [
                'label' => 'CAP Type'
            ])
            ->add('flushDate')
            ->add('isCapAlertDispatch', 'boolean', [
                'label' => 'Is email sent today'
            ]);
    }

}