<?php

namespace App\Admin\Sonata;

use App\Domain\Entity\Affiliate;
use App\Domain\Entity\Campaign;
use App\Domain\Service\AWSS3\S3Client;
use App\Domain\Service\CampaignService;
use App\Utils\UuidGenerator;
use App\Admin\Sonata\Traits\InitDoctrine;
use Doctrine\ORM\EntityRepository;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\Filesystem;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
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

    /**
     * Get instance of campaign
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function getNewInstance()
    {
        $instance = new Campaign(UuidGenerator::generate());
        $token = uniqid();
        $instance->setCampaignToken($token);

        return $instance;
    }

    /**
     * @param $obj
     */
    public function prePersist($obj)
    {
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
     * @param ContainerInterface $container
     */
    public function setContainer($container)
    {
        $this->container = $container;
        $this->initDoctrine($container);
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
            $adapter = new AwsS3Adapter($s3Client, 'playwing-appstore');

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
            ->add('affiliate')
            ->add('carriers', null, [], null, ['multiple' => true])
            ->add('bgColor')
            ->add('campaignToken')
            ->add('textColor')
            ->add('isPause');
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('uuid')
            ->add('affiliate', null, [
                'sortable'=>true,
                'sort_field_mapping'=> ['fieldName'=>'name'],
                'sort_parent_association_mappings' => [['fieldName'=>'affiliate']]
            ])
            ->add('isPause', null, [
                'label' => 'Pause'
            ])
            ->add('pausedCampaigns', null, [
                'label' => 'Paused by Carrier',
                'template' => '@Admin/Campaign/paused_campaigns.html.twig',
                'sortable'=>false
            ])
            ->add('landingUrl', null, [
                'label' => 'Landing page'
            ])
            ->add('carriers')
            ->add('_action', null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
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
            ->add('isPause', null,
                ['label' => 'Pause'])
            ->add('pausedCampaigns', null, [
                'label' => 'Paused by Carrier',
                'template' => '@Admin/Campaign/paused_campaigns.html.twig',
            ]);
    }

    /**
     * Generates a file name based on the supplied file.
     *
     * @param File $file
     * @return string
     */
    protected function generateFileName(File $file)
    {
        return sha1(uniqid(mt_rand())) . '.' . $file->guessExtension();
    }

    /**
     * @param Campaign $campaign
     */
    protected function generateTestLink(Campaign $campaign)
    {
        /**@var CampaignService $campaignService */
        $campaignService = $this->container->get('App\Domain\Service\CampaignService');
        $campaignService->generateTestLink($campaign);
    }

    /**
     * @param FormMapper $formMapper
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
                'class' => Affiliate::class,
                'choice_label' => 'name',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('a')->where('a.enabled=true');
                },
                'placeholder' => 'Select affiliate'
            ])
            ->add('carriers', null, [
                'required' => true
            ])
            ->end()
            ->add('ppd', MoneyType::class, [
                'label' => 'PPD',
                'required'=>true
            ])
            ->add('sub', MoneyType::class, [
                'label' => 'SUB',
                'required'=>true
            ])
            ->add('click', MoneyType::class, [
                'label' => 'CLICK',
                'required'=>true
            ])
            ->add('isPause', null, [
                'label' => 'Pause',
            ])
            ->end()
            ->end();
    }

    /**
     * @param FormMapper $formMapper
     */
    private function buildLandingPageSection(FormMapper $formMapper)
    {
        $formMapper
            ->tab('Landing page')
            ->with('', ['box_class' => 'box-solid'])
            ->add('image_file', FileType::class, [
                'required' => true,
                'label' => 'Main Image',
                'help' => $this->getImagePreviewHtml()
            ])
            ->add('bgColor', ColorType::class, [
                'attr' => ['style' => 'width: 50px'],
                'label' => 'Background color',
                'required' => true
            ])
            ->add('textColor', ColorType::class, [
                'attr' => ['style' => 'width: 50px'],
                'label' => 'Text color',
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
     */
    private function getImagePreviewHtml()
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
}