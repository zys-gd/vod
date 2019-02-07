<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 07.02.19
 * Time: 11:33
 */

namespace App\Controller;


use App\Domain\Entity\GameBuild;
use App\Domain\Repository\GameBuildRepository;
use App\Domain\Repository\GameRepository;
use App\Domain\Service\Games\DrmApkProvider;
use App\Domain\Service\Games\GameSerializer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use SubscriptionBundle\Service\SubscriptionExtractor;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

class GamesController extends AbstractController
{
    /**
     * @var GameRepository
     */
    private $gameRepository;
    /**
     * @var GameSerializer
     */
    private $gameSerializer;
    /**
     * @var GameBuildRepository
     */
    private $gameBuildRepository;
    /**
     * @var DrmApkProvider
     */
    private $drmApkProvider;
    /**
     * @var SubscriptionExtractor
     */
    private $extractor;


    /**
     * GamesController constructor.
     *
     * @param GameRepository        $gameRepository
     * @param GameSerializer        $gameSerializer
     * @param GameBuildRepository   $gameBuildRepository
     * @param DrmApkProvider        $drmApkProvider
     * @param SubscriptionExtractor $extractor
     */
    public function __construct(
        GameRepository $gameRepository,
        GameSerializer $gameSerializer,
        GameBuildRepository $gameBuildRepository,
        DrmApkProvider $drmApkProvider,
        SubscriptionExtractor $extractor
    )
    {
        $this->gameRepository      = $gameRepository;
        $this->gameSerializer      = $gameSerializer;
        $this->gameBuildRepository = $gameBuildRepository;
        $this->drmApkProvider      = $drmApkProvider;
        $this->extractor           = $extractor;
    }


    /**
     * @Route("/games/",name="game_category")
     * @Method("GET")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showCategoryContentAction()
    {

        $games = $this->gameRepository->findBatchOfGames();

        return $this->render('@App/Common/game_category_content.html.twig', ['games' => array_slice($games, 0, 4)]);


    }

    /**
     * @Route("game/{gameUuid}", name="game_content")
     * @Method("GET")
     * @param string $gameUuid
     */
    public function showGameContentAction(string $gameUuid='')
    {
        $game = $this->gameRepository->find($gameUuid);
        return $this->render('@App/Common/game.html.twig', ['game' => $game]);
    }

    /**
     * @Route("/games/load-more",name="load_more")
     * @Method("GET")
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function loadMoreAction(Request $request)
    {
        $offset = $request->get('offset', 0);

        $games = $this->gameRepository->findBatchOfGames((int)$offset, 4);

        $serializedData = [];

        foreach ($games as $game) {
            $serializedData[] = $this->gameSerializer->serializeGame($game);
        }

        return new JsonResponse([
            'games'  => $serializedData,
            'isLast' => count($games) < 4
        ]);
    }

    /**
     * @Route("/games/download", name="download_game")
     * @Method("GET")
     * @param Request $request
     * @return JsonResponse
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function downloadAction(Request $request)
    {

        if (!$id = $request->get('id', null)) {
            throw new BadRequestHttpException('Missing `id` parameter');
        }

        if (!$this->extractor->extractSubscriptionFromSession($request->getSession())) {
            throw new BadRequestHttpException('You are not subscribed');

        }

        /** @var GameBuild $gameBuild */
        $gameBuild = $this->gameBuildRepository->findOneBy(['game' => $id]);

        $link = $this->drmApkProvider->getDRMApkUrl($gameBuild);

        return new JsonResponse(['url' => $link]);
    }
}