<?php

namespace App\Admin\Sonata;

use App\Domain\Entity\DeviceDisplay;
use App\Domain\Entity\GameBuild;
use Aws\S3\S3Client;
use DeviceDetectionBundle\Service\DeviceInterface;
use ExtrasBundle\Utils\UuidGenerator;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\Filesystem;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class GameBuildAdmin
 */
class GameBuildAdmin extends AbstractAdmin
{
    /**
     * Constants used for labels
     */
    const OS_TYPE_FIELD_LABEL         = 'OS Type';
    const MIN_OS_VERSION_FIELD_LABEL  = 'Min. OS Version';
    const DEVICE_DISPLAYS_FIELD_LABEL = 'Screen sizes';
    const GAME_APK_FIELD_LABEL        = 'Game APK or VXP';
    const UPLOAD_PATH                 = 'uploads/builds';

    /**
     * @var bool
     */
    private $isApkUpdated = false;

    /**
     * @param GameBuild $gameBuild
     */
    public function prePersist($gameBuild)
    {
        $this->prepareGameBuilds($gameBuild);
    }

    /**
     * @param GameBuild $gameBuild
     */
    public function preUpdate($gameBuild)
    {
        $this->prepareGameBuilds($gameBuild);
    }

    /**
     * @param GameBuild $gameBuild
     */
    public function postPersist($gameBuild)
    {
        $this->addVersionToDRM($gameBuild);
    }

    /**
     * @param GameBuild $gameBuild
     */
    public function postUpdate($gameBuild)
    {
        if ($this->isApkUpdated) {
            $this->addVersionToDRM($gameBuild);
        }
    }

    /**
     * @return GameBuild
     *
     * @throws \Exception
     */
    public function getNewInstance(): GameBuild
    {
        return new GameBuild(UuidGenerator::generate());
    }

    /**
     * @param $errorElement
     * @param $object
     */
    public function validate($errorElement, $object)
    {
        /** @var GameBuild $gameBuild */
        $gameBuild = $this->getSubject();

        if (empty($gameBuild->getUuid())) {
            $errorElement
                ->with('game_apk')
                ->assertNotNull()
                ->end();
        };
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('uuid')
            ->add('osType', null, [
                'choices' => GameBuild::getAvailableOsTypes(),
                'label' => static::OS_TYPE_FIELD_LABEL
            ])
            ->add('minOsVersion', null, [
                'label' => static::MIN_OS_VERSION_FIELD_LABEL
            ])
            ->add('deviceDisplays', null, [
                'label' => static::DEVICE_DISPLAYS_FIELD_LABEL
            ]);
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('uuid')
            ->add('osType', 'choice', [
                'label' => static::OS_TYPE_FIELD_LABEL,
                'choices' => GameBuild::getAvailableOsTypes()
            ])
            ->add('minOsVersion', null, [
                'label' => static::MIN_OS_VERSION_FIELD_LABEL
            ])
            ->add('deviceDisplays', null, [
                'label' => static::DEVICE_DISPLAYS_FIELD_LABEL,
            ])
            ->add('_action', null, array(
                'actions' => array(
                    'show' => array(),
                    'edit' => array(),
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
            ->add('osType', ChoiceType::class, [
                'label' => static::OS_TYPE_FIELD_LABEL,
                'choices' => GameBuild::getAvailableOsTypes()
            ])
            ->add('minOsVersion', null, [
                'label' => static::MIN_OS_VERSION_FIELD_LABEL
            ])
            ->add('deviceDisplays', null, [
                'label' => static::DEVICE_DISPLAYS_FIELD_LABEL,
            ]);
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        /** @var GameBuild $gameBuild */
        $gameBuild = $this->getSubject();
        $requiredApk = $this->isCurrentRoute('create');

        $gameApkHelpText = $this->getConfigurationPool()->getContainer()->get('twig')->render(
            '@Admin/GameBuild/apk_help_text.html.twig',
            ['gameApk' => $gameBuild->getGameApk()]
        );

        $formMapper
            ->add('osType', ChoiceType::class, [
                'label' => static::OS_TYPE_FIELD_LABEL,
                'choices' => array(
                    DeviceInterface::OS_NAME_ANDROID => DeviceInterface::OS_TYPE_ANDROID,
                    DeviceInterface::OS_NAME_SYMBIAN => DeviceInterface::OS_TYPE_SYMBIAN,
                ),
            ])
            ->add('minOsVersion', null, [
                'label' => static::MIN_OS_VERSION_FIELD_LABEL,
                'help' => 'Must be in Semantic Versioning format. Example: "1.2.3" or "0.2.1".'
            ])
            ->add('deviceDisplays', EntityType::class, [
                'label' => static::DEVICE_DISPLAYS_FIELD_LABEL,
                'multiple' => true,
                'class' => DeviceDisplay::class
            ])
            ->add('gameApk', FileType::class, [
                'label' => static::GAME_APK_FIELD_LABEL,
                'required' => $requiredApk,
                'data_class' => null,
                'help' => $gameApkHelpText
            ]);
    }

    /**
     * @param GameBuild $gameBuild
     */
    private function prepareGameBuilds(GameBuild $gameBuild): void
    {
        $container = $this->getConfigurationPool()->getContainer();
        $gameApk = $gameBuild->getGameApk();

        if ($gameApk instanceof UploadedFile) {
            $fileName = md5(uniqid()) . '.' . $gameApk->getClientOriginalExtension();

            /** @var S3Client $s3Client */
            $s3Client = $container->get('App\Domain\Service\AWSS3\S3Client');
            $adapter = new AwsS3Adapter($s3Client, 'playwing-appstore');
            $filesystem = new Filesystem($adapter);

            $apkPath = sprintf("%s/%s", self::UPLOAD_PATH, $fileName);

            $filesystem->put($apkPath, file_get_contents($gameApk->getPathname()));
            $size = filesize($gameApk->getPathname());

            $gameBuild->setGameApk($fileName);
            $gameBuild->setApkSize($size);
            $gameBuild->updateVersion();
            $gameBuild->setApkVersion($gameBuild->getUuid());

            $this->isApkUpdated = true;
        }
    }

    /**
     * @param GameBuild $gameBuild
     */
    private function addVersionToDRM(GameBuild $gameBuild)
    {
        $container = $this->getConfigurationPool()->getContainer();

        $apkUrl = sprintf(
            "%s/%s/%s",
            $container->getParameter('s3_root_url'), self::UPLOAD_PATH, $gameBuild->getGameApk()
        );

        $drmApkVersion = $gameBuild->getUuid();

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $container->getParameter('drm_api_url') . 'add_version');
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, sprintf('_format=json&id=%s&location=%s', $drmApkVersion, $apkUrl));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-type: application/x-www-form-urlencoded',
            'X-AUTHORIZE-KEY: ' . $container->getParameter('drm_authorize_key')
        ));

        curl_exec($curl);
        curl_close($curl);
    }
}