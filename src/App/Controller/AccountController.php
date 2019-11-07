<?php

namespace App\Controller;

use App\Domain\Entity\MainCategory;
use App\Domain\Repository\CarrierRepository;
use CommonDataBundle\Service\TemplateConfigurator\Exception\TemplateNotFoundException;
use App\Domain\Repository\MainCategoryRepository;
use App\Domain\Repository\SubcategoryRepository;
use App\Domain\Repository\UploadedVideoRepository;
use App\Domain\Service\VideoProcessing\UploadedVideoSerializer;
use CommonDataBundle\Service\TemplateConfigurator\TemplateConfigurator;
use Doctrine\ORM\NonUniqueResultException;
use IdentificationBundle\Controller\ControllerWithISPDetection;
use IdentificationBundle\Identification\DTO\ISPData;
use SubscriptionBundle\Subscription\Common\SubscriptionExtractor;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController implements ControllerWithISPDetection, AppControllerInterface
{
    /**
     * @var SubscriptionExtractor
     */
    private $subscriptionExtractor;
    /**
     * @var TemplateConfigurator
     */
    private $templateConfigurator;
    /**
     * @var CarrierRepository
     */
    private $carrierRepository;
    /**
     * @var MainCategoryRepository
     */
    private $mainCategoryRepository;
    /**
     * @var SubcategoryRepository
     */
    private $subcategoryRepository;
    /**
     * @var UploadedVideoRepository
     */
    private $uploadedVideoRepository;
    /**
     * @var UploadedVideoSerializer
     */
    private $videoSerializer;

    /**
     * AccountController constructor.
     *
     * @param CarrierRepository       $carrierRepository
     * @param SubscriptionExtractor   $subscriptionExtractor
     * @param TemplateConfigurator    $templateConfigurator
     * @param MainCategoryRepository  $mainCategoryRepository
     * @param SubcategoryRepository   $subcategoryRepository
     * @param UploadedVideoRepository $uploadedVideoRepository
     * @param UploadedVideoSerializer $videoSerializer
     */
    public function __construct(
        CarrierRepository $carrierRepository,
        SubscriptionExtractor $subscriptionExtractor,
        TemplateConfigurator $templateConfigurator,
        MainCategoryRepository $mainCategoryRepository,
        SubcategoryRepository $subcategoryRepository,
        UploadedVideoRepository $uploadedVideoRepository,
        UploadedVideoSerializer $videoSerializer
    )
    {
        $this->subscriptionExtractor   = $subscriptionExtractor;
        $this->templateConfigurator    = $templateConfigurator;
        $this->carrierRepository       = $carrierRepository;
        $this->mainCategoryRepository  = $mainCategoryRepository;
        $this->subcategoryRepository   = $subcategoryRepository;
        $this->uploadedVideoRepository = $uploadedVideoRepository;
        $this->videoSerializer         = $videoSerializer;
    }

    /**
     * @Route("/account",name="account")
     * @param Request $request
     * @param ISPData $data
     *
     * @return Response
     *
     * @throws TemplateNotFoundException
     * @throws NonUniqueResultException
     * @throws \CommonDataBundle\Service\TemplateConfigurator\Exception\TemplateNotFoundException
     * @throws \Exception
     */
    public function accountAction(Request $request, ISPData $data)
    {
        $subscription = $this->subscriptionExtractor->extractSubscriptionFromSession($request->getSession());

        $templateParams = [];

        if (!is_null($subscription)) {
            $templateParams['subscriptionCreatedDate'] = $subscription->getCreated();
            // represent new idea of business team
            if ($subscription->isSubscribed()) {
                /** @var MainCategory $category */
                $category       = $this->mainCategoryRepository->find('15157409-49a4-4823-a7f9-654ac1d7c12f');
                $subcategories  = $this->subcategoryRepository->findBy(['parent' => $category]);
                $videos         = $this->uploadedVideoRepository->findNotExpiredBySubcategories($subcategories);
                $categoryVideos = [];
                foreach ($videos->getVideos() as $video) {
                    $categoryVideos[$video->getUuid()] = $this->videoSerializer->serializeShort($video);
                }

                $templateParams['categoryVideos'] = [$category->getTitle() => $categoryVideos];
                $templateParams['categories']     = [
                    $category->getTitle() => [
                        'uuid'  => $category->getUuid(),
                        'title' => $category->getTitle()
                    ]
                ];
            }
        }

        $template = $this->templateConfigurator->getTemplate('account', $data->getCarrierId());

        return $this->render($template, $templateParams);
    }
}
