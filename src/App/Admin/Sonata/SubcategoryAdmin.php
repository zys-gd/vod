<?php

namespace App\Admin\Sonata;

use App\Domain\Entity\MainCategory;
use App\Domain\Entity\Subcategory;
use App\Domain\Repository\SubcategoryRepository;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Class CategoryAdmin
 */
class SubcategoryAdmin extends AbstractAdmin
{
    /**
     * @var SubcategoryRepository
     */
    private $subcategoryRepository;

    /**
     * SubcategoryAdmin constructor
     *
     * @param string $code
     * @param string $class
     * @param string $baseControllerName
     * @param SubcategoryRepository $subcategoryRepository
     */
    public function __construct(
        string $code,
        string $class,
        string $baseControllerName,
        SubcategoryRepository $subcategoryRepository
    ) {
        $this->subcategoryRepository = $subcategoryRepository;

        parent::__construct($code, $class, $baseControllerName);
    }

    /**
     * @return array
     */
    public function getBatchActions(): array
    {
        return [];
    }

    /**
     * @param string|null $title
     * @param ExecutionContextInterface $context
     */
    public function validateTitle(?string $title, ExecutionContextInterface $context): void
    {
        /** @var Subcategory $subcategory */
        $subcategory = $this->getSubject();

        $uuid = $this->subcategoryRepository->getIdenticalSubcategoryUuid(trim($title));

        if ($uuid && $uuid !== $subcategory->getUuid()) {
            $context
                ->buildViolation('Identical subcategory title was found')
                ->addViolation();
        }
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('title')
            ->add('alias')
            ->add('parent', null, [], EntityType::class, [
                'class' => MainCategory::class
            ]);
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('uuid')
            ->add('parent', TextType::class, [
                'route' => ['name' => 'show']
            ])
            ->add('title')
            ->add('alias')
            ->add('_action', null, array(
                'actions' => array(
                    'show'   => array(),
                    'edit'   => array(),
                    'delete' => array(),
                )
            ));
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('uuid')
            ->add('parent', TextType::class)
            ->add('title');
    }

    /**
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->clearExcept(['list', 'edit', 'delete', 'show']);
        $collection->add('subcategoriesList', 'subcategoriesList');

        parent::configureRoutes($collection);
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('title', TextType::class, [
                'required' => true,
                'constraints' => [
                    new Callback([$this, 'validateTitle'])
                ]
            ]);
    }
}