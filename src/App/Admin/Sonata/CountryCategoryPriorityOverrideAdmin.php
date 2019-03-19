<?php

namespace App\Admin\Sonata;

use App\Domain\Entity\Country;
use App\Domain\Entity\CountryCategoryPriorityOverride;
use App\Domain\Entity\MainCategory;
use App\Domain\Repository\CountryCategoryPriorityOverrideRepository;
use App\Utils\UuidGenerator;
use Doctrine\ORM\EntityRepository;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Class CountryCategoryPriorityOverrideAdmin
 */
class CountryCategoryPriorityOverrideAdmin extends AbstractAdmin
{
    /**
     * @var array
     */
    protected $datagridValues = [
        '_sort_by' => 'menuPriority'
    ];

    /**
     * @var CountryCategoryPriorityOverride
     */
    private $countryOverrideRepository;

    /**
     * CountryCategoryPriorityOverrideAdmin constructor
     *
     * @param string $code
     * @param string $class
     * @param string $baseControllerName
     * @param CountryCategoryPriorityOverrideRepository $countryOverrideRepository
     */
    public function __construct(
        string $code,
        string $class,
        string $baseControllerName,
        CountryCategoryPriorityOverrideRepository $countryOverrideRepository
    ) {
        $this->countryOverrideRepository = $countryOverrideRepository;

        parent::__construct($code, $class, $baseControllerName);
    }

    /**
     * @return CountryCategoryPriorityOverride
     *
     * @throws \Exception
     */
    public function getNewInstance(): CountryCategoryPriorityOverride
    {
        return new CountryCategoryPriorityOverride(UuidGenerator::generate());
    }

    /**
     * @param int|null $menuPriority
     * @param ExecutionContextInterface $context
     */
    public function validatePriority(
        ?int $menuPriority,
        ExecutionContextInterface $context
    ): void
    {
        /** @var CountryCategoryPriorityOverride $countryPriorityOverride */
        $countryPriorityOverride = $this->getSubject();

        if ($menuPriority
            && $this->countryOverrideRepository->checkForIdenticalOverrides(
                $countryPriorityOverride->getMainCategory(),
                $countryPriorityOverride->getCountry(),
                $menuPriority)
        ) {
            $context
                ->buildViolation('Identical override for category and country was found')
                ->addViolation();
        }
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('country', null, [], EntityType::class, [
                'class' => Country::class,
                'query_builder' => function (EntityRepository $countryRepository) {
                    return $countryRepository
                        ->createQueryBuilder('c')
                        ->join(CountryCategoryPriorityOverride::class, 'cp', 'WITH', 'c.uuid = cp.country')
                        ->where('cp.uuid IS NOT NULL');
                }
            ]);
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('country')
            ->add('mainCategory')
            ->add('menuPriority')
            ->add('_action', null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => []
                ]
            ]);
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('uuid')
            ->add('country')
            ->add('mainCategory')
            ->add('menuPriority');
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('country', EntityType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please fill out this field'
                    ])
                ],
                'class' => Country::class,
                'placeholder' => 'Select country'
            ])
            ->add('mainCategory', EntityType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please fill out this field'
                    ])
                ],
                'class' => MainCategory::class,
                'placeholder' => 'Select main category'
            ])
            ->add('menuPriority', IntegerType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please fill out this field'
                    ]),
                    new Callback([$this, 'validatePriority'])
                ]
            ]);
    }
}