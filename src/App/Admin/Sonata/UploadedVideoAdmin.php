<?php

namespace App\Admin\Sonata;

use App\Domain\Entity\MainCategory;
use App\Domain\Entity\Subcategory;
use App\Domain\Entity\VideoPartner;
use App\Domain\Repository\SubcategoryRepository;
use App\Domain\Service\VideoProcessing\Connectors\CloudinaryConnector;
use App\Utils\UuidGenerator;
use Doctrine\ORM\EntityManager;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use App\Domain\Entity\UploadedVideo;
use Sonata\Form\Type\DateTimePickerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class UploadedVideoAdmin
 */
class UploadedVideoAdmin extends AbstractAdmin
{
    /**
     * @var CloudinaryConnector
     */
    private $cloudinaryConnector;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * UploadedVideoAdmin constructor
     *
     * @param string $code
     * @param string $class
     * @param string $baseControllerName
     * @param CloudinaryConnector $cloudinaryConnector
     * @param EntityManager $entityManager
     */
    public function __construct(
        string $code,
        string $class,
        string $baseControllerName,
        CloudinaryConnector $cloudinaryConnector,
        EntityManager $entityManager
    ) {
        $this->cloudinaryConnector = $cloudinaryConnector;
        $this->entityManager = $entityManager;

        parent::__construct($code, $class, $baseControllerName);
    }

    /**
     * @return array
     */
    public function getBatchActions(): array
    {
        return parent::getBatchActions();
    }

    /**
     * @param UploadedVideo $uploadedVideo
     */
    public function postRemove($uploadedVideo)
    {
        $this->cloudinaryConnector->destroyVideo($uploadedVideo->getRemoteId());
    }

    /**
     * @return UploadedVideo
     *
     * @throws \Exception
     */
    public function getNewInstance(): UploadedVideo
    {
        return new UploadedVideo(UuidGenerator::generate());
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('remoteId')
            ->add('title')
            ->add('expiredDate', 'doctrine_orm_datetime_range')
            ->add('subcategory', null, [], EntityType::class, [
                'class' => Subcategory::class
            ])
            ->add('status');
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        unset($this->listModes['mosaic']);

        $listMapper
            ->add('uuid')
            ->add('remoteId')
            ->add('title')
            ->add('subcategory')
            ->add('videoPartner')
            ->add('status', 'choice', [
                'editable' => false,
                'choices' => UploadedVideo::STATUSES
            ])
            ->add('createdDate', 'datetime', [
                'format' => 'Y-m-d H:i',
            ])
            ->add('expiredDate', 'datetime', [
                'format' => 'Y-m-d H:i',
            ])
            ->add('pause')
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
            ->add('remoteId')
            ->add('title')
            ->add('description')
            ->add('videoPartner')
            ->add('createdDate', 'datetime', [
                'format' => 'Y-m-d H:i',
            ])
            ->add('expiredDate', 'datetime', [
                'format' => 'Y-m-d H:i',
            ])
            ->add('status', 'choice', [
                'choices' => UploadedVideo::STATUSES
            ])
            ->add('thumbnail', null, [
                'template' => '@Admin/UploadedVideo/thumbnail.html.twig',
                'mapped'   => false,
                'label'    => 'Thumbnails'
            ])
            ->add('player', null, [
                'template' => '@Admin/UploadedVideo/player.html.twig',
                'mapped'   => false,
                'label'    => 'Video Preview'
            ])
            ->add('subcategory', null, [
                'associated_property' => 'title'
            ])
            ->add('createdAt')
            ->add('pause');
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
                    new NotBlank(),
                    new Length([
                        'max' => 255
                    ])
                ]
            ])
            ->add('videoPartner', EntityType::class, [
                'class' => VideoPartner::class
            ])
            ->add('status', ChoiceType::class, [
                'choices' => array_flip(UploadedVideo::STATUSES)
            ])
            ->add('description', TextareaType::class, [
                'required' => false
            ])
            ->add('expiredDate', DateTimePickerType::class, [
                'required' => false,
                'format' => 'Y-MM-dd HH:mm',
                'attr' => ['autocomplete' => 'off']
            ])
            ->add('pause');

        $builder = $formMapper->getFormBuilder();

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($builder) {
            /** @var UploadedVideo $uploadedVideo */
            $uploadedVideo = $event->getData();
            $form = $event->getForm();
            $formValues = $this->getRequest()->request->get($builder->getFormConfig()->getName());

            if ($uploadedVideo) {
                $mainCategory = empty($formValues)
                    ? $uploadedVideo->getSubcategory()->getParent()
                    : $this->entityManager->getRepository(MainCategory::class)->find($formValues['mainCategory']);

                $form
                    ->add('mainCategory', EntityType::class, [
                        'class' => MainCategory::class,
                        'data' => $mainCategory,
                        'required' => true,
                        'mapped' => false,
                        'placeholder' => 'Select main category'
                    ])
                    ->add('subcategory', EntityType::class, [
                        'query_builder' => function (SubcategoryRepository $subcategoryRepository) use ($mainCategory) {
                            $mainCategoryId = $mainCategory ? $mainCategory->getUuid() : null;

                            return $subcategoryRepository
                                ->createQueryBuilder('sc')
                                ->where('sc.parent = :mainId')
                                ->setParameter('mainId', $mainCategoryId);
                        },
                        'class' => Subcategory::class,
                        'required' => true,
                        'placeholder' => 'Select subcategory'
                    ]);
            }
        });
    }

    /**
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->clearExcept(['show', 'list', 'edit', 'delete', 'batch']);
        $collection->add('preUpload', 'preUpload');
        $collection->add('signature', 'signature');
        $collection->add('saveBaseVideoData', 'saveBaseVideoData');
        $collection->add('confirmVideos', 'confirmVideos');
        $collection->add('ping', 'ping');

        parent::configureRoutes($collection);
    }

    /**
     * @param $action
     * @param null $object
     *
     * @return array
     */
    public function configureActionButtons($action, $object = null): array
    {
        $list = parent::configureActionButtons($action, $object);
        $list['import']['template'] = '@Admin/UploadedVideo/PreUpload/pre_upload_button.html.twig';

        return $list;
    }

    protected function configureBatchActions($actions)
    {
        $actions['pause'] = [
            'ask_confirmation' => false
        ];

        return $actions;
    }
}