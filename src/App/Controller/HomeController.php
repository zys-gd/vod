<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 08.01.19
 * Time: 16:45
 */

namespace App\Controller;


use App\CarrierTemplate\TemplateConfigurator;
use App\Domain\Repository\CategoryRepository;
use App\Domain\Repository\UploadedVideoRepository;
use IdentificationBundle\Controller\ControllerWithIdentification;
use IdentificationBundle\Identification\DTO\ISPData;
use IdentificationBundle\Repository\CarrierRepositoryInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends AbstractController implements AppControllerInterface, ControllerWithIdentification
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
     * @var CategoryRepository
     */
    private $categoryRepository;
    /**
     * @var UploadedVideoRepository
     */
    private $videoRepository;

    /**
     * HomeController constructor.
     *
     * @param CarrierRepositoryInterface $carrierRepository
     * @param TemplateConfigurator       $templateConfigurator
     * @param CategoryRepository         $categoryRepository
     * @param UploadedVideoRepository    $videoRepository
     */
    public function __construct(
        CarrierRepositoryInterface $carrierRepository,
        TemplateConfigurator $templateConfigurator,
        CategoryRepository $categoryRepository,
        UploadedVideoRepository $videoRepository
    )
    {
        $this->carrierRepository    = $carrierRepository;
        $this->templateConfigurator = $templateConfigurator;
        $this->categoryRepository   = $categoryRepository;
        $this->videoRepository      = $videoRepository;
    }


    /**
     * @Route("/",name="index")
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request, ISPData $data)
    {
        $carrier    = $this->carrierRepository->findOneByBillingId($data->getCarrierId());
        $categories = $this->categoryRepository->findAll();
        $videos     = $this->videoRepository->findAll();

        return $this->render('@App/Common/home.html.twig', [
            'identificationData' => $request->getSession()->get('identification_data'),
            'templateHandler'    => $this->templateConfigurator->getTemplateHandler($carrier),
            'categories'         => $categories,
            'videos'             => $videos
        ]);
    }
}