<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 07.02.19
 * Time: 11:33
 */

namespace App\Controller;


use App\Domain\Repository\GameRepository;
use App\Domain\Service\Games\GameSerializer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
     * GamesController constructor.
     * @param GameRepository $gameRepository
     * @param GameSerializer $gameSerializer
     */
    public function __construct(GameRepository $gameRepository, GameSerializer $gameSerializer)
    {
        $this->gameRepository = $gameRepository;
        $this->gameSerializer = $gameSerializer;
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
            'isLast' => $games < 4
        ]);
    }
}