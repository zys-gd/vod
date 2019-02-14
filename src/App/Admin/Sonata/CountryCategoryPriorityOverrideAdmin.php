<?php

namespace App\Admin\Sonata;

use App\Domain\Entity\Country;
use App\Domain\Entity\CountryCategoryPriorityOverride;
use App\Domain\Entity\MainCategory;
use App\Utils\UuidGenerator;
use Doctrine\ORM\EntityRepository;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Validator\Constraints\NotBlank;

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
     * @return CountryCategoryPriorityOverride
     *
     * @throws \Exception
     */
    public function getNewInstance(): CountryCategoryPriorityOverride
    {
        return new CountryCategoryPriorityOverride(UuidGenerator::generate());
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
                'class' => Country::class,
                'required' => true,
                'placeholder' => 'Select country'
            ])
            ->add('mainCategory', EntityType::class, [
                'class' => MainCategory::class,
                'required' => true,
                'placeholder' => 'Select main category'
            ])
            ->add('menuPriority', IntegerType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank()
                ]
            ]);
    }
}