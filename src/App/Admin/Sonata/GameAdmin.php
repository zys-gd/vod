<?php

namespace App\Admin\Sonata;

use App\Admin\Sonata\Traits\InitDoctrine;
use App\Domain\Entity\Developer;
use App\Domain\Entity\Game;
use App\Domain\Service\AWSS3\S3Client;
use App\Domain\Service\Games\ImagePathProvider;
use App\Domain\Service\SimpleImageService;
use App\Utils\UuidGenerator;
use Knp\Menu\ItemInterface as MenuItemInterface;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\Filesystem;
use PriceBundle\Entity\Tier;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\Form\Type\CollectionType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser;

/**
 * Class GameAdmin
 */
class GameAdmin extends AbstractAdmin
{
    use InitDoctrine;

    /**
     * Constants used for tabs
     */
    const DETAILS_TAB_LABEL = 'Details';
    const SCREENSHOTS_TAB_LABEL = 'Screenshots';

    /**
     * Constants used for labels
     */
    const TITLE_FIELD_LABEL = 'Title';
    const SLUG_FIELD_LABEL = 'Slug';
    const PUBLISHED_FIELD_LABEL = 'Published';
    const DESCRIPTION_FIELD_LABEL = 'Description';
    const POSTER_FIELD_LABEL = 'Poster';
    const THUMBNAIL_FIELD_LABEL = 'Thumbnail';
    const DEVELOPER_FIELD_LABEL = 'Developer';
    const TIER_FIELD_LABEL = 'Tier';
    const RATING_FIELD_LABEL = 'Rating';
    const TAGS_FIELD_LABEL = 'Tags';
    const COUNTRIES_FIELD_LABEL = 'Deactivated countries';
    const CARRIERS_FIELD_LABEL = 'Deactivated carriers';
    const BUILDS_FIELD_LABEL = 'Builds';
    const SCREENSHOTS_FIELD_LABEL = 'Screenshots';
    const CREATED_FIELD_LABEL = 'Created';
    const UPDATED_FIELD_LABEL = 'Updated';
    const POSTER_PREVIEW_LABEL = 'Current poster (landscape image)';
    const THUMBNAIL_PREVIEW_LABEL = 'Current thumbnail (square icon)';

    const UPLOAD_PATH = 'uploads/cache/similar_game';
    const GAME_BUILD_PATH = 'builds';

    /**
     * @var S3Client
     */
    private $awsClient;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var ImagePathProvider
     */
    private $imagePathProvider;

    /**
     * GameAdmin constructor
     *
     * @param string $code
     * @param string $class
     * @param string $baseControllerName
     * @param ContainerInterface $container
     * @param ImagePathProvider $imagePathProvider
     */
    public function __construct(
        string $code,
        string $class,
        string $baseControllerName,
        ContainerInterface $container,
        ImagePathProvider $imagePathProvider
    ) {
        $this->initDoctrine($container);
        $this->container = $container;
        $this->awsClient = $this->container->get('App\Domain\Service\AWSS3\S3Client');
        $this->imagePathProvider = $imagePathProvider;

        parent::__construct($code, $class, $baseControllerName);
    }

    /**
     * @return Game
     *
     * @throws \Exception
     */
    public function getNewInstance(): Game
    {
        return new Game(UuidGenerator::generate());
    }

    /**
     * @param string $context
     * @return ProxyQueryInterface
     */
    public function createQuery($context = 'list'): ProxyQueryInterface
    {
        $query = parent::createQuery($context);
        $rootAlias = $query->getRootAliases()[0];

        $query->andWhere(
            $query->expr()->eq($rootAlias.'.isBookmark', ':isBookmark')
        );

        $query->setParameter(':isBookmark', '0');

        return $query;
    }

    /**
     * @param $game
     *
     * @throws \Exception
     */
    public function preUpdate ($game)
    {
        $this->prepareGame($game);
    }

    /**
     * @param $game
     *
     * @throws \Exception
     */
    public function prePersist ($game)
    {
        $this->prepareGame($game);
    }

    /**
     * @param Game $game
     *
     * @throws \Exception
     */
    protected function prepareGame(Game $game)
    {
        $this->prepareScreenshots($game);
        $this->preparePoster($game);
        $this->prepareThumbnail($game);
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters (DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('uuid')
            ->add('title', null, [
                'label' => static::TITLE_FIELD_LABEL
            ])
            ->add('published', null, [
                'label' => static::PUBLISHED_FIELD_LABEL
            ])
            ->add(
                'rating',
                null,
                ['label' => static::RATING_FIELD_LABEL],
                ChoiceType::class,
                ['choices' => Game::getAvailableRatings(true)]
            )
            ->add('created', 'doctrine_orm_datetime_range', [
                'label' => static::CREATED_FIELD_LABEL
            ])
            ->add('updated', 'doctrine_orm_datetime_range', [
                'label' => static::UPDATED_FIELD_LABEL
            ]);
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields (ListMapper $listMapper)
    {
        $listMapper
            ->add('uuid')
            ->add('title', null, [
                'label' => static::TITLE_FIELD_LABEL
            ])
            ->add('published', null, [
                'label' => static::PUBLISHED_FIELD_LABEL
            ])
            ->add('rating', ChoiceType::class, [
                'label' => static::RATING_FIELD_LABEL,
                'choices' => Game::getAvailableRatings()
            ])
            ->add('created', null, [
                'label' => static::CREATED_FIELD_LABEL
            ])
            ->add('updated', null, [
                'label' => static::UPDATED_FIELD_LABEL
            ])
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
    protected function configureShowFields (ShowMapper $showMapper)
    {
        $showMapper
            ->add('uuid')
            ->add('title', null, [
                'label' => static::TITLE_FIELD_LABEL
            ])
            ->add('published', null, [
                'label' => static::PUBLISHED_FIELD_LABEL
            ])
            ->add('description', null, [
                'label' => static::DESCRIPTION_FIELD_LABEL
            ])
            ->add('thumbnail', null, [
                'label' => static::THUMBNAIL_FIELD_LABEL
            ])
            ->add('icon', null, [
                'label' => static::POSTER_FIELD_LABEL
            ])
            ->add('developer', null, [
                'label' => static::DEVELOPER_FIELD_LABEL
            ])
            ->add('tier', null, [
                'label' => static::TIER_FIELD_LABEL
            ])
            ->add('rating', ChoiceType::class, [
                'label' => static::RATING_FIELD_LABEL,
                'choices' => Game::getAvailableRatings()
            ])
            ->add('builds', null, [
                'label' => static::BUILDS_FIELD_LABEL
            ])
            ->add('images', null, [
                'label' => static::SCREENSHOTS_FIELD_LABEL
            ])
            ->add('created', null, [
                'label' => static::CREATED_FIELD_LABEL
            ])
            ->add('updated', null, [
                'label' => static::UPDATED_FIELD_LABEL
            ]);
    }

    /**
     * @param MenuItemInterface $menu
     * @param string $action
     * @param AdminInterface|null $childAdmin
     */
    protected function configureTabMenu(MenuItemInterface $menu, $action, AdminInterface $childAdmin = null)
    {
        $childAdmins = $this->getChildren();

        if ($action === 'edit' && !empty($childAdmins)) {
            foreach ($childAdmins as $admin) {

                /** @var AdminInterface $admin */
                $menu->addChild($admin->getLabel(), [
                    'label' => static::BUILDS_FIELD_LABEL,
                    'uri' => $admin->generateUrl('list')
                ]);
            }
        }
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields (FormMapper $formMapper)
    {
        $this->buildDetailsSection($formMapper);
        $this->buildScreenShotSection($formMapper);
    }

    /**
     * @param FormMapper $formMapper
     */
    private function buildDetailsSection(FormMapper $formMapper): void
    {
        /** @var Game $game */
        $game = $this->getSubject();

        $isImagesRequired = $this->isCurrentRoute('create');
        $posterImagePreview = '';
        $thumbnailImagePreview = '';

        if ($game && $game->getIcon()) {
            $posterImagePreview = $this->container->get('twig')->render(
                '@Admin/Game/image_preview.html.twig',
                [
                    'label' => self::POSTER_PREVIEW_LABEL,
                    'url' => $this->imagePathProvider->getGamePosterPath($game->getIcon())
                ]
            );
        }

        if ($game && $game->getThumbnail()) {
            $thumbnailImagePreview = $this->container->get('twig')->render(
                '@Admin/Game/image_preview.html.twig',
                [
                    'label' => self::THUMBNAIL_PREVIEW_LABEL,
                    'url' => $this->imagePathProvider->getGameSmallThumbnailPath($game->getThumbnail())
                ]
            );
        }

        $formMapper
            ->with(static::DETAILS_TAB_LABEL)
            ->add('title', null, [
                'label' => static::TITLE_FIELD_LABEL
            ])
            ->add('description', null, [
                'label' => static::DESCRIPTION_FIELD_LABEL
            ])
            ->add('thumbnail_file', FileType::class, [
                'required' => $isImagesRequired,
                'data_class' => null,
                'label' => static::THUMBNAIL_FIELD_LABEL,
                'help' => $thumbnailImagePreview
            ])
            ->add('icon_file', FileType::class, [
                'required' => $isImagesRequired,
                'data_class' => null,
                'label' => static::POSTER_FIELD_LABEL,
                'help' => $posterImagePreview
            ])
            ->add('developer', ModelAutocompleteType::class, [
                'class' => Developer::class,
                'property' => 'name',
                'multiple' => false,
                'label' => static::DEVELOPER_FIELD_LABEL,
                'placeholder' => 'Select developer'
            ])
            ->add('tier', EntityType::class, [
                'class' => Tier::class,
                'required' => true,
                'label' => static::TIER_FIELD_LABEL,
                'placeholder' => 'Select tier'
            ])
            ->add('rating', ChoiceType::class, [
                'placeholder' => 'Select rating',
                'label' => static::RATING_FIELD_LABEL,
                'choices' => Game::getAvailableRatings(true)
            ])
            ->add('published', null, [
                'label' => static::PUBLISHED_FIELD_LABEL,
            ])
            ->end();
    }

    /**
     * @param FormMapper $formMapper
     */
    private function buildScreenShotSection(FormMapper $formMapper): void
    {
        $formMapper
            ->with(static::SCREENSHOTS_TAB_LABEL)
            ->add('images', CollectionType::class, [
                'label' => false,
                'by_reference' => false,
                'btn_add' => 'Add new' ,
                'type_options' => array(
                    'btn_delete' => true,
                )
            ], [
                'edit' => 'inline',
                'inline' => 'standard'
            ])
            ->end();
    }

    /**
     * @param Game $game
     *
     * @throws \Exception
     */
    private function preparePoster(Game $game): void
    {
        $file = $game->getIconFile();

        if (!is_null($file)) {
            $size = $this->container->getParameter('game_poster_small');
            $name = $this->generateFileName($file);

            $game->setIcon($name);

            $adapter = new AwsS3Adapter($this->awsClient, 'playwing-appstore');
            /** @var Filesystem $filesystem */
            $filesystem = new Filesystem($adapter);

            $mimeType = MimeTypeGuesser::getInstance()->guess($file->getPathname());

            /** @var SimpleImageService $image */
            $image = $this->container->get('App\Domain\Service\SimpleImageService');
            $image->load($file->getPathname());
            $image->resizeToWidth($size['width']);

            if ($image->getHeight() > $size['height']) {
                $image->resizeToHeight($size['height']);
            }

            $image->save($file->getPathname());

            $handle = fopen($file->getPathname(), 'r');

            $filesystem->putStream($this->imagePathProvider->getGamePosterPath($name, true), $handle, [
                'mimetype' => $mimeType,
            ]);
        }
    }

    /**
     * @param Game $game
     *
     * @throws \Exception
     */
    private function prepareThumbnail (Game $game): void
    {
        $file = $game->getThumbnailFile();

        if (!is_null($file)) {
            $size = $this->container->getParameter('game_thumbnail_medium');

            $name = $this->generateFileName($file);

            $game->setThumbnail($name);

            $adapter = new AwsS3Adapter($this->awsClient, 'playwing-appstore');
            /** @var Filesystem $filesystem */
            $filesystem = new Filesystem($adapter);

            /** @var SimpleImageService $image */
            $image = $this->container->get('App\Domain\Service\SimpleImageService');
            $image->load($file->getPathname());
            $image->resizeToWidth($size['width']);

            if ($image->getHeight() > $size['height']) {
                $image->resizeToHeight($size['height']);
            }

            $image->save($file->getPathname());

            $mimeType = MimeTypeGuesser::getInstance()->guess($file->getPathname());

            $handle = fopen($file->getPathname(), 'r');

            $filesystem->putStream($this->imagePathProvider->getGameSmallThumbnailPath($name, true), $handle, [
                'mimetype' => $mimeType,
            ]);
        }
    }

    /**
     * @param Game $game
     *
     * @throws \Exception
     */
    private function prepareScreenshots (Game $game): void
    {
        $images = $game->getImages();

        foreach ($images as $image) {
            $size = $this->container->getParameter('game_screenshot_medium');

            /** @var File $file */
            $file = $image->getFile();

            if (!is_null($file)) {
                $name = $this->generateFileName($file);

                $image->setName($name);

                $adapter = new AwsS3Adapter($this->awsClient, 'playwing-appstore');
                /** @var Filesystem $filesystem */
                $filesystem = new Filesystem($adapter);

                /** @var SimpleImageService $image */
                $image = $this->container->get('App\Domain\Service\SimpleImageService');
                $image->load($file->getPathname());
                $image->resizeToWidth($size['width']);

                if ($image->getHeight() > $size['height']) {
                    $image->resizeToHeight($size['height']);
                }

                $image->save($file->getPathname());

                $mimeType = MimeTypeGuesser::getInstance()->guess($file->getPathname());

                $handle = fopen($file->getPathname(), 'r');

                $filesystem->putStream($this->imagePathProvider->getGameScreenshotPath($name, true), $handle, [
                    'mimetype' => $mimeType,
                ]);
            }
        }
    }

    /**
     * @param File $file
     *
     * @return string
     */
    private function generateFileName (File $file): string
    {
        return sha1(uniqid(mt_rand())) . '.' . $file->guessExtension();
    }
}