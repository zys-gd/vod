<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 31.01.19
 * Time: 12:47
 */

namespace DataFixtures;


use App\Domain\Entity\UploadedVideo;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadUploadedVideosData extends AbstractFixture implements DependentFixtureInterface
{

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on
     *
     * @return array
     */
    public function getDependencies()
    {
        return [LoadSubcategoriesData::class];
    }

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     * @throws \Exception
     */
    public function load(ObjectManager $manager)
    {
        $data = \DataFixtures\Utils\FixtureDataLoader::loadDataFromJSONFile('uploaded_videos.json');;

        foreach ($data as $row) {

            $uuid            = $row['uuid'];
            $status          = $row['status'];
            $remoteUrl       = $row['remoteUrl'];
            $remoteId        = $row['remoteId'];
            $createdAt       = $row['createdAt'];
            $title           = $row['title'];
            $description     = $row['description'];
            $thumbnails      = $row['thumbnails'];
            $subcategoryUuid = $row['subcategory']['uuid'];

            $video = new UploadedVideo($uuid);

            $video->setStatus($status);
            $video->setRemoteUrl($remoteUrl);
            $video->setRemoteId($remoteId);
            $video->setCreatedAt(new \DateTime($createdAt));
            $video->setTitle($title);
            $video->setDescription($description);
            $video->setThumbnails($thumbnails);

            $video->setSubcategory($this->getReference(sprintf('subcategory_%s', $subcategoryUuid)));

            $manager->persist($video);


        }

        $manager->flush();
    }
}