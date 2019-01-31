<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 08.01.19
 * Time: 16:45
 */

namespace App\Controller;


use App\CarrierTemplate\TemplateConfigurator;
use App\Domain\Entity\UploadedVideo;
use App\Domain\Repository\MainCategoryRepository;
use App\Domain\Repository\UploadedVideoRepository;
use IdentificationBundle\Controller\ControllerWithIdentification;
use IdentificationBundle\Identification\DTO\ISPData;
use IdentificationBundle\Repository\CarrierRepositoryInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends AbstractController implements AppControllerInterface
{
    /**
     * @var CarrierRepositoryInterface
     */
    private $carrierRepository;
    /**
     * @var TemplateConfigurator
     */
    private $templateConfigurator;
    /**
     * @var MainCategoryRepository
     */
    private $mainCategoryRepository;
    /**
     * @var UploadedVideoRepository
     */
    private $videoRepository;

    /**
     * HomeController constructor.
     *
     * @param CarrierRepositoryInterface $carrierRepository
     * @param TemplateConfigurator       $templateConfigurator
     * @param MainCategoryRepository     $mainCategoryRepository
     * @param UploadedVideoRepository    $videoRepository
     */
    public function __construct(
        CarrierRepositoryInterface $carrierRepository,
        TemplateConfigurator $templateConfigurator,
        MainCategoryRepository $mainCategoryRepository,
        UploadedVideoRepository $videoRepository
    )
    {
        $this->carrierRepository      = $carrierRepository;
        $this->templateConfigurator   = $templateConfigurator;
        $this->mainCategoryRepository = $mainCategoryRepository;
        $this->videoRepository        = $videoRepository;
    }


    /**
     * @Route("/",name="index")
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request, ISPData $data)
    {
        $carrier = $this->carrierRepository->findOneByBillingId($data->getCarrierId());
        $videos  = $this->videoRepository->findAll();

        $categories     = [];
        $categoryVideos = [];
        /** @var UploadedVideo[] $videos */
        foreach ($videos as $video) {
            $categoryEntity = $video->getSubcategory()->getParent();
            $categoryKey    = $categoryEntity->getTitle();

            $categoryVideos[$categoryKey][$video->getUuid()] = [
                'uuid'       => $video->getUuid(),
                'title'      => $video->getTitle(),
                'publicId'   => $video->getRemoteId(),
                'thumbnails' => $video->getThumbnails()
            ];

            $categories[$categoryKey] = [
                'uuid'  => $categoryEntity->getUuid(),
                'title' => $categoryEntity->getTitle(),
            ];

        }

        return $this->render('@App/Common/home.html.twig', [
            'templateHandler' => $this->templateConfigurator->getTemplateHandler($carrier),
            'categoryVideos'  => $categoryVideos,
            'categories'      => $categories
        ]);
    }
}