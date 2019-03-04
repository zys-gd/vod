<?php

namespace DataFixtures;

use App\Domain\Entity\UploadedVideo;
use DataFixtures\Utils\FixtureDataLoader;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class LoadUploadedVideosData
 */
class LoadUploadedVideosData extends AbstractFixture implements DependentFixtureInterface
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     * @throws \Exception
     */
    public function load(ObjectManager $manager)
    {
        $data = FixtureDataLoader::loadDataFromJSONFile('uploaded_videos.json');

        foreach ($data as $row) {
            $uuid            = $row['uuid'];
            $status          = $row['status'];
            $remoteUrl       = $row['remoteUrl'];
            $remoteId        = $row['remoteId'];
            $createdDate     = $row['createdDate'];
            $expiredDate     = $row['expiredDate'];
            $title           = $row['title'];
            $description     = $row['description'];
            $thumbnails      = $row['thumbnails'];
            $subcategoryUuid = $row['subcategory']['uuid'];

            $video = new UploadedVideo($uuid);

            $video->setStatus($status);
            $video->setRemoteUrl($remoteUrl);
            $video->setRemoteId($remoteId);
            $video->setCreatedDate(new \DateTime($createdDate));
            $video->setExpiredDate($expiredDate ? new \DateTime($expiredDate) : null);
            $video->setTitle($title);
            $video->setDescription($description);
            $video->setThumbnails($thumbnails);
            $video->setSubcategory($this->getReference(sprintf('subcategory_%s', $subcategoryUuid)));

            $manager->persist($video);
        }

        $manager->flush();
    }

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on
     *
     * @return array
     */
    public function getDependencies()
    {
        return [
            LoadSubcategoriesData::class
        ];
    }
}