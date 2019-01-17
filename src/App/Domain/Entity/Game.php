<?php

namespace App\Domain\Entity;

use App\Domain\Constants\ConstCategoriesId;
use Doctrine\ORM\PersistentCollection;
use phpDocumentor\Reflection\Types\Boolean;
use PriceBundle\Entity\Tier;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\File\File;
use Playwing\DiffToolBundle\Entity\Interfaces\HasUuid;

class Game implements HasUuid
{
    public static $baseUrl = "/";
    /**
     * Constants used for determining the location of different resources for this entity
     */
    const RESOURCE_POSTERS = 'images/game_icons';
    const RESOURCE_THUMBNAILS = 'images/game_thumbnails';

    /**
     * Constants used for different types of tags which can be applied to games
     */
    const TAG_TYPE_NEW = 1;
    const TAG_TYPE_HOT = 2;

    /**
     * Constants used to determine the name of the tags which are used in the admin panel
     */
    const TAG_NAME_NEW = 'New';
    const TAG_NAME_HOT = 'Hot';

    /**
     * Constants used for different types of ratings which can be applied to games
     */
    const RATING_TYPE_2_STARS = 2;
    const RATING_TYPE_3_STARS = 3;
    const RATING_TYPE_4_STARS = 4;
    const RATING_TYPE_5_STARS = 5;

    /**
     * Constants used to determine the name of the ratings which are used in the admin panel
     */
    const RATING_NAME_2_STARS = 'A';
    const RATING_NAME_3_STARS = 'AA';
    const RATING_NAME_4_STARS = 'AAA-';
    const RATING_NAME_5_STARS = 'AAA+';

    /**
     * @var string
     */
    private $uuid;

    /**
     * @var string
     */
    private $title;

    /**
     * @var boolean
     */
    private $published = true;

    /**
     * @var integer
     */
    private $tags = 0;

    /**
     * @var integer
     */
    private $rating;

    /**
     * @var string
     */
    private $icon;

    /**
     * @var File
     */
    private $icon_file;

    /**
     * @var string
     */
    private $thumbnail;

    /**
     * @var File
     */
    private $thumbnail_file;

    /**
     * @var string
     */
    private $description;

    /**
     * @var ArrayCollection
     */
    private $categoryGameAssociations;

    /**
     * @var Developer
     */
    private $developer;

    /**
     * @var Collection
     */
    private $images;

    /**
     * @var string
     */
    protected $locale;

    /**
     * @var ArrayCollection
     */
    protected $translations;

    /**
     * @var Tier
     */
    private $tier;

    /**
     * @var string
     */
    private $currency;

    /**
     * @var Collection
     */
    private $builds;

    /**
     * @var \DateTime
     */
    private $created;

    /**
     * @var \DateTime
     */
    private $updated;

    /**
     * @var \DateTime
     */
    private $deletedAt;

    /**
     * @var Collection
     */
    private $deactivatedCountries;

    /**
     * @var Collection
     */
    private $deactivatedCarriers;

    /**
     * @var Collection
     */
    private $categoryGameCountryLinks;

    /**
     * @var Boolean
     */
    private $isBookmark = false;

    const BYTES_IN_MEGABYTE = 1048576;

    /**
     * Returns a list with all available tags
     *
     * @param bool $flip
     * @return array
     */
    public static function getAvailableTags($flip = false)
    {
        $tags = [
            Game::TAG_TYPE_NEW => Game::TAG_NAME_NEW,
            Game::TAG_TYPE_HOT => Game::TAG_NAME_HOT
        ];

        return $flip ? array_flip($tags) : $tags;
    }

    /**
     * Returns a list with all available ratings
     *
     * @param bool $flip
     * @return array
     */
    public static function getAvailableRatings($flip = false)
    {
        $tags = [
            Game::RATING_TYPE_2_STARS => Game::RATING_NAME_2_STARS,
            Game::RATING_TYPE_3_STARS => Game::RATING_NAME_3_STARS,
            Game::RATING_TYPE_4_STARS => Game::RATING_NAME_4_STARS,
            Game::RATING_TYPE_5_STARS => Game::RATING_NAME_5_STARS,
        ];

        return $flip ? array_flip($tags) : $tags;
    }

    /**
     * Game constructor.
     *
     * @param string $uuid
     */
    public function __construct(string $uuid)
    {
        $this->uuid = $uuid;
        $this->builds = new ArrayCollection();
        $this->translations = new ArrayCollection();
        $this->images = new ArrayCollection();
        $this->categoryGameAssociations = new ArrayCollection();
        $this->deactivatedCountries = new ArrayCollection();
        $this->deactivatedCarriers = new ArrayCollection();
    }

    /**
     * @param string $uuid
     */
    public function setUuid(string $uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }

    /**
     * @return Collection
     */
    public function getDeactivatedCountries(): Collection
    {
        return $this->deactivatedCountries;
    }

    /**
     * @param Collection $deactivatedCountries
     */
    public function setDeactivatedCountries($deactivatedCountries)
    {
        $this->deactivatedCountries = $deactivatedCountries;
    }

    /**
     * Add deactivated Country
     *
     * @param Country $deactivatedCountries
     *
     * @return Game
     */
    public function addDeactivatedCountries(Country $deactivatedCountries)
    {
        $this->deactivatedCountries[] = $deactivatedCountries;

        return $this;
    }

    /**
     * Remove country from deactivated countries array
     *
     * @param Country $deactivatedCountries
     */
    public function removeDeactivatedCountries(Country $deactivatedCountries)
    {
        $this->deactivatedCountries->removeElement($deactivatedCountries);
    }

    /**
     * @return Collection
     */
    public function getDeactivatedCarriers(): Collection
    {
        return $this->deactivatedCarriers;
    }

    /**
     * @param Collection $deactivatedCarriers
     */
    public function setDeactivatedCarriers(Collection $deactivatedCarriers)
    {
        $this->deactivatedCarriers = $deactivatedCarriers;
    }

    /**
     * Add deactivated Carrier
     *
     * @param Carrier $deactivatedCarriers
     *
     * @return Game
     */
    public function addDeactivatedCarriers(Carrier $deactivatedCarriers)
    {
        $this->deactivatedCarriers[] = $deactivatedCarriers;

        return $this;
    }

    /**
     * Remove carrier from deactivated carriers array
     *
     * @param Carrier $deactivatedCarriers
     */
    public function removeDeactivatedCarriers(Carrier $deactivatedCarriers)
    {
        $this->deactivatedCarriers->removeElement($deactivatedCarriers);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getTitle();
    }
    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Game
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Set published
     *
     * @param boolean $published
     *
     * @return Game
     */
    public function setPublished($published)
    {
        $this->published = $published;

        return $this;
    }

    /**
     * Get published
     *
     * @return boolean
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * Get icon
     *
     * @return string
     */
    public function getIcon()
    {
        return $this->icon;
    }


    /**
     * Get icon path
     *
     * @return string
     */
    public function getIconPath()
    {
        return static::RESOURCE_POSTERS . '/' . $this->getIcon();
    }

    /**
     * Get icon file
     *
     * @return File
     */
    public function getIconFile()
    {
        return $this->icon_file;
    }

    /**
     * Set icon
     *
     * @param string $icon
     *
     * @return Game
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * Set icon file
     *
     * @param File $file
     * @return Game
     */
    public function setIconFile(File $file)
    {
        $this->icon_file = $file;

        return $this;
    }

    /**
     * Get thumbnail
     *
     * @return string
     */
    public function getThumbnail()
    {
        return $this->thumbnail;
    }

    /**
     * Get thumbnail path
     *
     * @return string
     */
    public function getThumbnailPath()
    {
        return static::RESOURCE_THUMBNAILS . '/' . $this->getThumbnail();
    }

    /**
     * Get thumbnail file
     *
     * @return File
     */
    public function getThumbnailFile()
    {
        return $this->thumbnail_file;
    }


    /**
     * Set thumbnail
     *
     * @param string $thumbnail
     *
     * @return Game
     */
    public function setThumbnail($thumbnail)
    {
        $this->thumbnail = $thumbnail;

        return $this;
    }

    /**
     * Set thumbnail file
     *
     * @param File $file
     * @return Game
     */
    public function setThumbnailFile(File $file)
    {
        $this->thumbnail_file = $file;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Game
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Set downloads
     *
     * @param integer $downloads
     *
     * @return Game
     */
    public function setDownloads($downloads)
    {
        $this->downloads = $downloads;

        return $this;
    }

    /**
     * Get categories
     *
     * @return array
     */
    public function getCategories()
    {
        $categories = [];

        foreach ($this->getCategoryGameAssociations() as $association) {
            $categories[] = $association->getCategory();
        }

        return $categories;
    }

    /**
     * Get first category
     *
     * @return Category
     */
    public function getFirstCategory(): Category
    {
        $category = null;
        $all_games = null;
        foreach ($this->getCategoryGameAssociations() as $association) {
            /** @var Category $current_category */
            $current_category = $association->getCategory();
            if (!in_array($current_category->getAlias(), [ConstCategoriesId::TOP_GAMES, ConstCategoriesId::NEW_GAMES, ConstCategoriesId::ALL_GAMES, ConstCategoriesId::HOMEPAGE_SLIDER])) {
                $category = $current_category;
                break;
            }
            elseif ($current_category->getAlias() == ConstCategoriesId::ALL_GAMES) {
                $all_games = $current_category;
            };
        }
        if (is_null($category)) {
            $category = $all_games;
        };
        return $category;
    }

    /**
     * Get categoryGameAssociations
     *
     * @return CategoryGameAssociation[]|Collection
     */
    public function getCategoryGameAssociations()
    {
        return $this->categoryGameAssociations;
    }

    /**
     * Add a categoryGameAssociation
     *
     * @param CategoryGameAssociation $categoryGameAssociation
     *
     * @return Game
     */
    public function addCategoryGameAssociation(CategoryGameAssociation $categoryGameAssociation)
    {
        $this->categoryGameAssociations[] = $categoryGameAssociation;

        return $this;
    }

    /**
     * Remove a categoryGameAssociation
     *
     * @param CategoryGameAssociation $categoryGameAssociation
     */
    public function removeCategoryGameAssociation(CategoryGameAssociation $categoryGameAssociation)
    {
        $this->categoryGameAssociations->removeElement($categoryGameAssociation);
    }

    /**
     * Get developer
     *
     * @return Developer
     */
    public function getDeveloper()
    {
        return $this->developer;
    }

    /**
     * Set developer
     *
     * @param Developer $developer
     *
     * @return Game
     */
    public function setDeveloper(Developer $developer = null)
    {
        $this->developer = $developer;

        return $this;
    }

    /**
     * Get images
     *
     * @return Collection
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * Add image
     *
     * @param GameImage $image
     *
     * @return Game
     */
    public function addImage(GameImage $image)
    {
        $this->images[] = $image;
        $image->setGame($this);

        return $this;
    }

    /**
     * @param $imagesCollection
     */
    public function setImages($imagesCollection)
    {
        $this->images = $imagesCollection;
    }

    /**
     * Remove image
     *
     * @param GameImage $image
     */
    public function removeImage(GameImage $image)
    {
        $this->images->removeElement($image);
    }

    /**
     * @param $locale
     */
    public function setTranslatableLocale($locale)
    {
        $this->locale = $locale;
    }

    /**
     * Returns the game's price tier
     *
     * @return Tier | null
     */
    public function getTier()
    {
        return $this->tier;
    }

    /**
     * Sets a price tier for the game
     *
     * @param Tier $tier
     * @return Game
     */
    public function setTier(Tier $tier): Game
    {
        $this->tier = $tier;
        return $this;
    }

    /**
     * Returns the game's curreny
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Sets the currency form the game's price
     * @param string $currency
     * @return Game
     */
    public function setCurrency(string $currency): Game
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * Add build
     *
     * @param GameBuild $build
     *
     * @return Game
     */
    public function addBuild(GameBuild $build)
    {
        $this->builds[] = $build;

        $build->setGame($this);

        return $this;
    }

    /**
     * Remove build
     *
     * @param GameBuild $build
     */
    public function removeBuild(GameBuild $build)
    {
        $this->builds->removeElement($build);
    }

    /**
     * Get builds
     *
     * @return Collection
     */
    public function getBuilds()
    {
        return $this->builds;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return Game
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     *
     * @return Game
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set deletedAt
     *
     * @param \DateTime $deletedAt
     *
     * @return Game
     */
    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    /**
     * Get deletedAt
     *
     * @return \DateTime
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * Set tags
     *
     * @param integer $tags
     *
     * @return Game
     */
    public function setTags($tags)
    {
        if (is_array($tags)) {

            $sum = 0;

            foreach ($tags as $tag) {
                $sum |= $tag;
            }

            $tags = $sum;
        }

        if (is_null($tags)) {

            $tags = 0;
        }

        $this->tags = $tags;

        return $this;
    }

    /**
     * Get tags
     *
     * @return array
     */
    public function getTags()
    {
        $gameTags = [];
        $availableTags = static::getAvailableTags();

        foreach ($availableTags as $tagType => $tagName) {

            if (!($this->tags & $tagType)) {
                continue;
            }

            $gameTags[$tagName] = $tagType;
        }

        return $gameTags;
    }


    /**
     * Add categoryGameCountryLink
     *
     * @param CategoryGameCountryLink $categoryGameCountryLink
     *
     * @return Game
     */
    public function addCategoryGameCountryLink(CategoryGameCountryLink $categoryGameCountryLink)
    {
        $this->categoryGameCountryLinks[] = $categoryGameCountryLink;

        return $this;
    }

    /**
     * Remove categoryGameCountryLink
     *
     * @param CategoryGameCountryLink $categoryGameCountryLink
     */
    public function removeCategoryGameCountryLink(CategoryGameCountryLink $categoryGameCountryLink)
    {
        $this->categoryGameCountryLinks->removeElement($categoryGameCountryLink);
    }

    /**
     * Get categoryGameCountryLinks
     *
     * @return Collection
     */
    public function getCategoryGameCountryLinks()
    {
        return $this->categoryGameCountryLinks;
    }

    /**
     * Set rating
     *
     * @param integer $rating
     *
     * @return Game
     */
    public function setRating($rating)
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * Get rating
     *
     * @return integer
     */
    public function getRating()
    {
        return $this->rating;
    }

    public function getApkSize()
    {
        /** @var GameBuild[] $builds */
        $builds = $this->getBuilds()->toArray();
        if (count($builds) == 0) {
            return "apk not found. 0 ";
        };
        return number_format($builds[0]->getApkSize() / Game::BYTES_IN_MEGABYTE, 1, ',', ' ');
    }

    //TODO: make it
    public function getSlug()
    {
        return $this->getName();
        /** @var PersistentCollection $trem */
        // $trem = $this->getTranslations('name', 'en');
        // $t = $trem->get(0);
        // $ret = strtolower($t ? $t->getContent() : $this->getName());
        // $ret = preg_replace("/[^a-z0-9_']/i", "-", $ret);
        // $ret = str_replace(['"', "'", '--'], ['-', '-', '-'], $ret);
        // return $ret;
    }


    /**
     * custom flag, non db related
     * @var bool
     */
    private $_isDownloadEnabled = false;
    private $_isDownloadDisabled = false;
    private $_isRedownloadableEnabled = false;
    private $_isRedownloadableDisabled = false;
    private $_isAllowTrial = false;

    public function isDownloadable()
    {
        return $this->_isDownloadEnabled || $this->_isRedownloadableEnabled;
    }

    public function isDownloadEnabled()
    {
        return $this->_isDownloadEnabled;
    }

    public function isDownloadDisabled()
    {
        return $this->_isDownloadDisabled;
    }

    public function isRedownloadableEnabled()
    {
        /*  var_dump($this->_isRedownloadableEnabled);*/
        return $this->_isRedownloadableEnabled;
    }

    public function isRedownloadableDisabled()
    {
        return $this->_isRedownloadableDisabled;
    }

    public function isAllowTrial()
    {
        return $this->_isAllowTrial;
    }

    public function isDownloadableDebug()
    {
        echo '<pre>' . $this->uuid
            . ' de:' . ($this->_isDownloadEnabled ? 1 : 0)
            . ' dd:' . ($this->_isDownloadDisabled ? 1 : 0)
            . ' re:' . ($this->_isRedownloadableEnabled ? 1 : 0)
            . ' rd:' . ($this->_isRedownloadableDisabled ? 1 : 0)
            . ' at:' . ($this->_isAllowTrial ? 1 : 0)
            . '</pre>';
    }

    public function setIsDownloadable($isDownloadEnabled,
        $isDownloadDisabled,
        $isRedownloadableEnabled,
        $isRedownloadableDisabled,
        $isAllowTrial)
    {
        $this->_isDownloadEnabled = $isDownloadEnabled;
        $this->_isDownloadDisabled = $isDownloadDisabled;
        $this->_isRedownloadableEnabled = $isRedownloadableEnabled;
        $this->_isRedownloadableDisabled = $isRedownloadableDisabled;
        $this->_isAllowTrial = $isAllowTrial;
    }


    /**
     * @return string
     */
    public function getName()
    {
        return $this->getTitle();
    }

    /**
     * @return string
     */
    public function getPublisher()
    {
        return $this->getDeveloper()->getName();
    }

    /**
     * Set isBookmark
     *
     * @param boolean $isBookmark
     *
     * @return Game
     */
    public function setIsBookmark($isBookmark)
    {
        $this->isBookmark = $isBookmark;

        return $this;
    }

    /**
     * Get isBookmark
     *
     * @return boolean
     */
    public function getIsBookmark()
    {
        return $this->isBookmark;
    }

    /**
     * @return string
     * This is how we generate the token to identify traffic intended for aff campaigns.
     * /?cmpId=token
     * We don't need to store the computed token anywhere inside the DB to identify a specific campaign,
     * because we get its ID it by decoding the token. This applies only when there is no enforced ID from affiliate.
     * This method is called only by configureListFields() inside AppBundle\Admin\CampaignAdmin
     *
     *
     * The parameter names are hardcoded, and should be read from app/config/parameters.yml
     */
    public function getPageUrl()
    {
        return "http://" . $_SERVER['SERVER_NAME'] . "/trial?bt=" . base64_encode(json_encode(array('name' => $this->getName(), 'id' => $this->getUuid())));
    }
}