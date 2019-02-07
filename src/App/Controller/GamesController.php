<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 07.02.19
 * Time: 11:33
 */

namespace App\Controller;


use App\Domain\Repository\GameRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class GamesController extends AbstractController
{
    /**
     * @var GameRepository
     */
    private $gameRepository;


    /**
     * GamesController constructor.
     * @param GameRepository $gameRepository
     */
    public function __construct(GameRepository $gameRepository)
    {
        $this->gameRepository = $gameRepository;
    }


    /**
     * @Route("/games/",name="game_category")
     * @Method("GET")
     * @return void
     */
    public function showCategoryContentAction()
    {

        $games = $this->gameRepository->findAll();

        return $this->render('@App/Common/game_category_content.html.twig', ['games' => array_slice($games, 0, 4)]);


    }

    /**
     * @Route("/load-more/{offset}",name="load_more")
     * @Method("GET")
     * @return void
     */
    public function loadMoreAction(string $offset)
    {

    }
}