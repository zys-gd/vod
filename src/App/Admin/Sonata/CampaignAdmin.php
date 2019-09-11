<?php

namespace App\Admin\Sonata;

use App\Admin\Form\Type\CampaignScheduleType;
use App\Admin\Sonata\Traits\InitDoctrine;
use App\Domain\Entity\Affiliate;
use App\Domain\Entity\Campaign;
use App\Domain\Entity\MainCategory;
use App\Domain\Service\AWSS3\S3Client;
use App\Domain\Service\Campaign\CampaignService;
use Doctrine\ORM\EntityRepository;
use ExtrasBundle\Utils\UuidGenerator;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\Filesystem;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ChoiceFieldMaskType;
use Sonata\AdminBundle\Form\Type\CollectionType;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser;

/**
 * Class CampaignAdmin
 */
class CampaignAdmin extends AbstractAdmin
{
    use InitDoctrine;

    /**
     * @var ContainerInterface
     */
    private $container;

    protected $datagridValues = [
        '_page' => 1,
        '_sort_order' => 'DESC',
        '_sort_by' => 'dateCreated',
    ];

    /**
     * CampaignAdmin constructor
     *
     * @param string             $code
     * @param string             $class
     * @param string             $baseControllerName
     * @param ContainerInterface $container
     */
    public function __construct(string $code, string $class, string $baseControllerName, ContainerInterface $container)
    {
        $this->container = $container;
        $this->initDoctrine($container);

        parent::__construct($code, $class, $baseControllerName);
    }

    /**
     * Get instance of campaign
     * @return mixed
     * @throws \Exception
     */
    public function getNewInstance(): Campaign
    {
        $instance = new Campaign(UuidGenerator::generate());

        return $instance;
    }

    /**
     * @param $obj
     */
    public function prePersist($obj)
    {
        $adminUser = $this->getConfigurationPool()->getContainer()->get('security.token_storage')->getToken()->getUser();
        $obj->setDateCreated(date_create());
        $obj->setCreator($adminUser->getUsername());
        $this->preUpdate($obj);
    }

    /**
     * @param $object
     */
    public function postPersist($object)
    {
        $this->generateTestLink($object);
        parent::update($object);
    }

    /**
     * @param $obj
     */
    public function preUpdate($obj)
    {
        $this->prepareImage($obj);
        $this->generateTestLink($obj);
    }

    /**
     * @param Campaign $campaign
     */
    protected function prepareImage(Campaign $campaign)
    {
        $file = $campaign->getImageFile();

        if (!is_null($file) && $file) {

            $name = $this->generateFileName($file);

            /** @var S3Client $s3Client */
            $s3Client = $this->container->get('App\Domain\Service\AWSS3\S3Client');
            $adapter  = new AwsS3Adapter($s3Client, 'playwing-appstore');

            /** @var Filesystem $filesystem */
            $filesystem = new Filesystem($adapter);

            $campaign->setImageName($name);
            $mimeType = MimeTypeGuesser::getInstance()->guess($file->getPathname());

            $handle = fopen($file->getPathname(), 'r');

            $filesystem->putStream(sprintf("%s/%s", Campaign::RESOURCE_IMAGE, $name), $handle, [
                'mimetype' => $mimeType,
            ]);
        }
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('uuid')
            ->add('mainCategory', null, ['label' => 'Category'])
            ->add('affiliate')
            ->add('carriers', null, [], null, ['multiple' => true])
            ->add('bgColor')
            ->add('campaignToken')
            ->add('textColor')
            ->add('isPause')
            ->add('isLpOff')
            ->add('isClickableSubImage')
            ->add('zeroCreditSubAvailable');
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('uuid')
            ->add('mainCategory', null, [
                'label' => 'Category'
            ])
            ->add('affiliate', null, [
                'sortable'                         => true,
                'sort_field_mapping'               => ['fieldName' => 'name'],
                'sort_parent_association_mappings' => [['fieldName' => 'affiliate']]
            ])
            ->add('isPause', null, [
                'label' => 'Pause'
            ])
            ->add('pausedCampaigns', null, [
                'label'    => 'Paused by Carrier',
                'template' => '@Admin/Campaign/paused_campaigns.html.twig',
                'sortable' => false
            ])
            ->add('landingUrl', null, [
                'label' => 'Landing page'
            ])
            ->add('isLpOff')
            ->add('zeroCreditSubAvailable')
            ->add('freeTrialSubscription')
            ->add('isClickableSubImage', null, [
                'label' => 'Clickable image'
            ])
            ->add('carriers')
            ->add('dateCreated')
            ->add('_action', null, [
                'actions' => [
                    'show'   => [],
                    'edit'   => [],
                    'delete' => [],
                    'clone'  => [
                        'template' => '@Admin/Campaign/clone_btn.html.twig',
                    ],
                ]
            ]);

        if (!$this->isGranted('ROLE_GUEST')) {
            $listMapper->remove('_action');
        }
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('uuid')
            ->add('affiliate.name')
            ->add('carriers')
            ->add('bgColor')
            ->add('textColor')
            ->add('isLpOff', null, [
                'label' => 'Turn off LP showing',
                'help'  => 'If consent page exist, then show it. Otherwise will try to subscribe'
            ])
            ->add('isPause', null,
                ['label' => 'Pause'])
            ->add('zeroCreditSubAvailable')
            ->add('freeTrialSubscription')
            ->add('isClickableSubImage', null, [
                'label' => 'Clickable image'
            ])
            ->add('pausedCampaigns', null, [
                'label'    => 'Paused by Carrier',
                'template' => '@Admin/Campaign/paused_campaigns.html.twig',
            ]);
    }

    /**
     * Generates a file name based on the supplied file.
     *
     * @param File $file
     *
     * @return string
     */
    protected function generateFileName(File $file): string
    {
        return sha1(uniqid(mt_rand())) . '.' . $file->guessExtension();
    }

    /**
     * @param Campaign $campaign
     */
    protected function generateTestLink(Campaign $campaign)
    {
        /**@var CampaignService $campaignService */
        $campaignService = $this->container->get('App\Domain\Service\Campaign\CampaignService');
        $campaignService->generateTestLink($campaign);
    }

    /**
     * @param FormMapper $formMapper
     *
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $this->buildGeneralSection($formMapper);
        $this->buildLandingPageSection($formMapper);
    }

    /**
     * @param FormMapper $formMapper
     */
    private function buildGeneralSection(FormMapper $formMapper)
    {
        $formMapper
            ->tab('General')
            ->with('', ['box_class' => 'box-solid'])
            ->add('affiliate', EntityType::class, [
                'class'         => Affiliate::class,
                'choice_label'  => 'name',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('a')->where('a.enabled=true');
                },
                'placeholder'   => 'Select affiliate'
            ])
            ->add('carriers', null, [
                'required' => true
            ])
            ->end()
            ->add('ppd', MoneyType::class, [
                'label'    => 'PPD',
                'required' => true
            ])
            ->add('sub', MoneyType::class, [
                'label'    => 'SUB',
                'required' => true
            ])
            ->add('click', MoneyType::class, [
                'label'    => 'CLICK',
                'required' => true
            ])
            ->add('isPause', null, [
                'label' => 'Pause',
            ])
            ->add('zeroCreditSubAvailable')
            ->add('isClickableSubImage', null, [
                'label' => 'Clickable image'
            ])
            ->add('isLpOff', ChoiceFieldMaskType::class, [
                'choices' => [
                    'No'  => 0,
                    'Yes' => 1
                ],
                'label'   => 'Turn off LP showing',
                'map'     => [
                    1 => ['schedule'],
                ],
                'help'    => 'If consent page exist, then show it. Otherwise will try to subscribe'
            ])
            ->add('schedule', CollectionType::class, [
                'entry_type'   => CampaignScheduleType::class,
                'allow_delete' => true,
                'allow_add'    => true,
                'prototype'    => true,
                'by_reference' => false
            ])
            ->add('freeTrialSubscription')
            ->end()
            ->end();
    }

    /**
     * @param FormMapper
     *
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    private function buildLandingPageSection(FormMapper $formMapper)
    {
        $imagePreview = $this->getImagePreviewHtml();

        $formMapper
            ->tab('Landing page')
            ->with('', ['box_class' => 'box-solid'])
            ->add('mainCategory', EntityType::class, [
                'class'       => MainCategory::class,
                'placeholder' => 'Select main category',
                'label'       => 'Category to be displayed'
            ])
            ->add('image_file', FileType::class, [
                'required' => empty($imagePreview),
                'label'    => 'Main Image',
                'help'     => $imagePreview
            ])
            ->add('bgColor', ColorType::class, [
                'attr'     => ['style' => 'width: 50px'],
                'label'    => 'Background color',
                'data'     => '#ffffff',
                'required' => true
            ])
            ->add('textColor', ColorType::class, [
                'attr'     => ['style' => 'width: 50px'],
                'label'    => 'Text color',
                'required' => true
            ])
            ->add('isPause', null, [
                'label' => 'Pause',
            ])
            ->end()
            ->end();
    }

    /**
     * @return string
     *
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    private function getImagePreviewHtml(): string
    {
        /** @var Campaign $subject */
        $subject = $this->getSubject();

        if ($subject && $subject->getImageName()) {
            $imagePath = $this->container->getParameter('images_base_url') . '/' . $subject->getImagePath();

            return $this->container->get('twig')->render(
                '@Admin/Campaign/campaign_banner_preview.html.twig',
                ['url' => $imagePath]
            );
        }

        return '';
    }

    /**
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->add('clone', $this->getRouterIdParameter() . '/clone');
        $collection->add('clone_confirm', $this->getRouterIdParameter() . '/clone_confirm');

        parent::configureRoutes($collection);
    }

    /**
     * @param array $actions
     *
     * @return array
     */
    protected function configureBatchActions($actions)
    {
        $actions['pause'] = [
            'ask_confirmation' => false
        ];

        $actions['unpause'] = [
            'ask_confirmation' => false,
            'label' => 'Remove from pause'
        ];

        $actions['enableOneClick'] = [
            'ask_confirmation' => false
        ];

        $actions['disableOneClick'] = [
            'ask_confirmation' => false
        ];

        $actions['enableClickableImage'] = [
            'ask_confirmation' => false
        ];

        $actions['disableClickableImage'] = [
            'ask_confirmation' => false
        ];

        return $actions;
    }
}