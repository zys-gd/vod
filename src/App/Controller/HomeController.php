<?php

namespace App\Controller;

use App\Domain\DTO\BatchOfGames;
use App\Domain\Repository\GameRepository;
use App\Domain\Service\HomepageVideoListProvider;
use App\Piwik\ContentStatisticSender;
use CommonDataBundle\Service\TemplateConfigurator\TemplateConfigurator;
use IdentificationBundle\Controller\ControllerWithIdentification;
use IdentificationBundle\Controller\ControllerWithISPDetection;
use IdentificationBundle\Identification\DTO\ISPData;
use IdentificationBundle\Identification\Service\AlreadySubscribedIdentFinisher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class HomeController
 */
class HomeController extends AbstractController implements
    ControllerWithISPDetection,
    AppControllerInterface,
    ControllerWithIdentification
{
    /**
     * @var TemplateConfigurator
     */
    private $templateConfigurator;

    /**
     * @var GameRepository
     */
    private $gameRepository;

    /**
     * @var ContentStatisticSender
     */
    private $contentStatisticSender;


    /**
     * @var AlreadySubscribedIdentFinisher
     */
    private $alreadySubscribedIdentFinisher;
    /**
     * @var HomepageVideoListProvider
     */
    private $homepageVideoListProvider;

    /**
     * HomeController constructor.
     *
     * @param TemplateConfigurator           $templateConfigurator
     * @param GameRepository                 $gameRepository
     * @param ContentStatisticSender         $contentStatisticSender
     * @param AlreadySubscribedIdentFinisher $alreadySubscribedIdentFinisher
     * @param HomepageVideoListProvider      $homepageVideoListProvider
     */
    public function __construct(
        TemplateConfigurator $templateConfigurator,
        GameRepository $gameRepository,
        ContentStatisticSender $contentStatisticSender,
        AlreadySubscribedIdentFinisher $alreadySubscribedIdentFinisher,
        HomepageVideoListProvider $homepageVideoListProvider
    )
    {
        $this->templateConfigurator           = $templateConfigurator;
        $this->gameRepository                 = $gameRepository;
        $this->contentStatisticSender         = $contentStatisticSender;
        $this->alreadySubscribedIdentFinisher = $alreadySubscribedIdentFinisher;
        $this->homepageVideoListProvider      = $homepageVideoListProvider;
    }

    /**
     * @Route("/",name="index")
     * @param Request $request
     * @param ISPData $data
     *
     * @return Response
     *
     * @throws \Exception
     */
    public function indexAction(Request $request, ISPData $data)
    {
        /**
         * @var BatchOfGames $games
         */
        $games = $this->gameRepository->findBatchOfGames(0, 2);

        if ($this->alreadySubscribedIdentFinisher->needToHandle($request)) {
            $this->alreadySubscribedIdentFinisher->tryToIdentify($request);
        }

        [$categorizedVideos, $indexedCategoryData] = $this->homepageVideoListProvider->getVideosForHomepage($data);

        $this->contentStatisticSender->trackVisit($request->getSession());

        $template = $this->templateConfigurator->getTemplate('home', $data->getCarrierId());

        return $this->render($template, [
            'categoryVideos' => array_slice($categorizedVideos, 1, 3),
            'categories'     => $indexedCategoryData,
            'sliderVideos'   => array_slice($categorizedVideos, 0, 1),
            'games'          => $games->getGames()
        ]);
    }

}