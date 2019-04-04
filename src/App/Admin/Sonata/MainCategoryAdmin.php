<?php

namespace App\Admin\Sonata;

use App\Domain\Entity\MainCategory;
use App\Domain\Repository\MainCategoryRepository;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Class MainCategoryAdmin
 */
class MainCategoryAdmin extends AbstractAdmin
{
    /**
     * @var MainCategoryRepository
     */
    private $mainCategoryRepository;

    /**
     * MainCategoryAdmin constructor
     *
     * @param string $code
     * @param string $class
     * @param string $baseControllerName
     * @param MainCategoryRepository $mainCategoryRepository
     */
    public function __construct(
        string $code,
        string $class,
        string $baseControllerName,
        MainCategoryRepository $mainCategoryRepository
    ) {
        $this->mainCategoryRepository = $mainCategoryRepository;

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
     * @param int|null $menuPriority
     * @param ExecutionContextInterface $context
     */
    public function validateMenuPriority(?int $menuPriority, ExecutionContextInterface $context): void
    {
        /** @var MainCategory $mainCategory */
        $mainCategory = $this->getSubject();

        $duplicatedRecord = $this->mainCategoryRepository->findOneBy(['menuPriority' => $menuPriority]);

        if ($duplicatedRecord && $mainCategory->getUuid() !== $duplicatedRecord->getUuid()) {
            $context
                ->buildViolation('Identical menu priority was found')
                ->addViolation();
        }
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('title');
    }


    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('uuid')
            ->add('title')
            ->add('menuPriority')
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
            ->add('title')
            ->add('menuPriority')
            ->add('subcategories');
    }

    /**
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->clearExcept(['list', 'edit', 'delete', 'show']);

        parent::configureRoutes($collection);
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('title', TextType::class, [
                'required' => true
            ])
            ->add('menuPriority', IntegerType::class, [
                'required' => true,
                'constraints' => [
                    new Callback([$this, 'validateMenuPriority'])
                ]
            ]);
    }
}