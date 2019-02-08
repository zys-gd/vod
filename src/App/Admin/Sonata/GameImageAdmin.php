<?php

namespace App\Admin\Sonata;

use App\Domain\Entity\GameImage;
use App\Domain\Service\Games\ImagePathProvider;
use App\Utils\UuidGenerator;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\FileType;

/**
 * Class GameImageAdmin
 */
class GameImageAdmin extends AbstractAdmin
{
    /**
     * Constants used for labels
     */
    const TITLE_FIELD_LABEL = 'Title';
    const NAME_FIELD_LABEL  = 'Name';
    const FILE_FIELD_LABEL  = 'Image';
    const IMAGE_PREVIEW_LABEL = 'Current screenshot';

    /**
     * @var ImagePathProvider
     */
    private $imagePathProvider;

    /**
     * GameImageAdmin constructor
     *
     * @param string $code
     * @param string $class
     * @param string $baseControllerName
     * @param ImagePathProvider $imagePathProvider
     */
    public function __construct(
        string $code,
        string $class,
        string $baseControllerName,
        ImagePathProvider $imagePathProvider
    ) {
        $this->imagePathProvider = $imagePathProvider;

        parent::__construct($code, $class, $baseControllerName);
    }

    /**
     * @return GameImage
     *
     * @throws \Exception
     */
    public function getNewInstance(): GameImage
    {
        return new GameImage(UuidGenerator::generate());
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('uuid')
            ->add('name', null, [
                'label' => static::NAME_FIELD_LABEL
            ])
            ->add('_action', null, [
                'actions' => [
                    'show' => [],
                    'delete' => [],
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
            ->add('name', null, [
                'label' => static::NAME_FIELD_LABEL
            ]);
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        /** @var GameImage $gameImage */
        $gameImage = $this->getSubject();

        $isImagesRequired = $this->isCurrentRoute('create');
        $imagePreview = '';

        if ($gameImage && $gameImage->getName()) {
            $imagePreview = $this->getConfigurationPool()->getContainer()->get('twig')->render(
                '@Admin/Game/image_preview.html.twig',
                [
                    'label' => self::IMAGE_PREVIEW_LABEL,
                    'url' => $this->imagePathProvider->getGameScreenshotPath($gameImage->getName())
                ]
            );
        }

        $formMapper->add('file', FileType::class, [
            'required' => $isImagesRequired,
            'label' => static::FILE_FIELD_LABEL,
            'help' => $imagePreview
        ]);
    }
}