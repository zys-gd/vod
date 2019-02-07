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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
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
     * GamesController constructor.
     *
     * @param GameRepository      $gameRepository
     * @param GameSerializer      $gameSerializer
     * @param GameBuildRepository $gameBuildRepository
     */
    public function __construct(GameRepository $gameRepository,
        GameSerializer $gameSerializer,
        GameBuildRepository $gameBuildRepository,
        DrmApkProvider $drmApkProvider)
    {
        $this->gameRepository = $gameRepository;
        $this->gameSerializer = $gameSerializer;
        $this->gameBuildRepository = $gameBuildRepository;
        $this->drmApkProvider = $drmApkProvider;
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
     * @Route("/load-more",name="load_more")
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
            'games' => $serializedData,
            'isLast' => $games < 4
        ]);
    }

    /**
     * @Route("/download/game/{gameUuid}", name="download_game")
     * @param string $gameUuid
     *
     * @return RedirectResponse
     */
    public function downloadAction(string $gameUuid)
    {
        /** @var GameBuild $gameBuild */
        $gameBuild = $this->gameBuildRepository->findOneBy(['game' => $gameUuid]);
        $link = $this->drmApkProvider->getDRMApkUrl($gameBuild);
        return new RedirectResponse($link);
    }
}