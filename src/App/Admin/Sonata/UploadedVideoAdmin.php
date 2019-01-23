<?php

namespace App\Admin\Sonata;

use App\Admin\Sonata\Traits\InitDoctrine;
use App\Domain\Entity\Category;
use App\Domain\Service\VideoProcessing\VideoManager;
use App\Utils\UuidGenerator;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use App\Domain\Entity\UploadedVideo;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UploadedVideoAdmin extends AbstractAdmin
{
    use InitDoctrine;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var VideoManager
     */
    private $videoManager;

    /**
     * UploadedVideoAdmin constructor
     *
     * @param string $code
     * @param string $class
     * @param string $baseControllerName
     * @param VideoManager $videoManager
     * @param ContainerInterface $container
     */
    public function __construct(
        string $code,
        string $class,
        string $baseControllerName,
        VideoManager $videoManager,
        ContainerInterface $container
    ) {
        $this->container = $container;
        $this->videoManager = $videoManager;

        $this->initDoctrine($container);

        parent::__construct($code, $class, $baseControllerName);
    }

    /**
     * @return array
     */
    public function getBatchActions()
    {
        return [];
    }

    /**
     * @param UploadedVideo $uploadedVideo
     */
    public function postRemove($uploadedVideo)
    {
        $this->videoManager->destroyUploadedVideo($uploadedVideo);
    }

    /**
     * @return UploadedVideo
     *
     * @throws \Exception
     */
    public function getNewInstance()
    {
        return new UploadedVideo(UuidGenerator::generate());
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('status')
            ->add('remoteId')
            ->add('title');
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
            ->add('status', ChoiceType::class, [
                'choices' => UploadedVideo::STATUSES
            ])
            ->add('createdAt')
            ->add('_action', null, array(
                'actions' => array(
                    'show'   => array(),
                    'edit'   => array(),
                    'delete' => array(),
                )
            ));
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $mainCategories = $this->em->getRepository(Category::class)->findBy(['parent' => null]);

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
            ->add('description', TextareaType::class, [
                'required' => false
            ])
            ->add('mainCategory', EntityType::class, [
                'class' => Category::class,
                'mapped' => false,
                'choices' => $mainCategories,
                'placeholder' => 'Select main category',
                'required' => true
            ]);

        $builder = $formMapper->getFormBuilder();

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            /** @var UploadedVideo $uploadedVideo */
            $uploadedVideo = $event->getData();
            $category = $uploadedVideo ? $uploadedVideo->getCategory() : null;

            if ($category ) {
                $this->appendCategoryField($event->getForm(), $category);
            }
        });

        $builder->get('mainCategory')->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();
            $mainCategory = $form->getData();

            $this->appendCategoryField($form->getParent(), $mainCategory);
        });
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
            ->add('category', null, [
                'associated_property' => 'title'
            ])
            ->add('createdAt');
    }

    /**
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->clearExcept(['show', 'list', 'edit', 'delete']);
        $collection->add('upload', 'upload');

        parent::configureRoutes($collection);
    }

    /**
     * @param $action
     * @param null $object
     *
     * @return array
     */
    public function configureActionButtons($action, $object = null)
    {
        $list = parent::configureActionButtons($action, $object);
        $list['import']['template'] = '@Admin/UploadedVideo/upload_button.html.twig';

        return $list;
    }

    /**
     * @param FormInterface $form
     * @param Category $mainCategory
     */
    private function appendCategoryField(FormInterface $form, Category $mainCategory)
    {
        $subCategories = $this->em->getRepository(Category::class)->findBy(['parent' => $mainCategory]);

        if (count($subCategories) > 0) {
            $form->add('category', EntityType::class, [
                'class' => Category::class,
                'choices' => $subCategories,
                'required' => true,
                'placeholder' => 'Select category'
            ]);
        }
    }
}