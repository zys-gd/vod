<?php

namespace App\Controller;

use App\Domain\Entity\Game;
use App\Domain\Entity\GameBuild;
use App\Domain\Entity\GameImage;
use App\Domain\Repository\GameBuildRepository;
use App\Domain\Repository\GameRepository;
use App\Domain\Service\Games\DrmApkProvider;
use App\Domain\Service\Games\ExcludedGamesProvider;
use App\Domain\Service\Games\GameImagesSerializer;
use App\Domain\Service\Games\GameSerializer;
use App\Domain\Service\ContentStatisticSender;
use IdentificationBundle\Identification\DTO\ISPData;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Service\SubscriptionExtractor;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

class GamesController extends AbstractController implements AppControllerInterface
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
    private $subscriptionExtractor;
    /**
     * @var GameImagesSerializer
     */
    private $gameImagesSerializer;
    /**
     * @var ExcludedGamesProvider
     */
    private $excludedGamesProvider;
    /**
     * @var ContentStatisticSender
     */
    private $contentStatisticSender;

    /**
     * GamesController constructor.
     *
     * @param GameRepository $gameRepository
     * @param GameSerializer $gameSerializer
     * @param GameBuildRepository $gameBuildRepository
     * @param DrmApkProvider $drmApkProvider
     * @param SubscriptionExtractor $extractor
     * @param GameImagesSerializer $gameImagesSerializer
     * @param ExcludedGamesProvider $excludedGamesProvider
     * @param ContentStatisticSender $contentStatisticSender
     */
    public function __construct(
        GameRepository $gameRepository,
        GameSerializer $gameSerializer,
        GameBuildRepository $gameBuildRepository,
        DrmApkProvider $drmApkProvider,
        SubscriptionExtractor $extractor,
        GameImagesSerializer $gameImagesSerializer,
        ExcludedGamesProvider $excludedGamesProvider,
        ContentStatisticSender $contentStatisticSender
    )
    {
        $this->gameRepository        = $gameRepository;
        $this->gameSerializer        = $gameSerializer;
        $this->gameBuildRepository   = $gameBuildRepository;
        $this->drmApkProvider        = $drmApkProvider;
        $this->subscriptionExtractor = $extractor;
        $this->gameImagesSerializer  = $gameImagesSerializer;
        $this->excludedGamesProvider = $excludedGamesProvider;
        $this->contentStatisticSender  = $contentStatisticSender;
    }


    /**
     * @Route("/games/",name="game_category")
     * @Method("GET")
     *
     * @param ISPData $data
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showCategoryContentAction(ISPData $data)
    {
        $games = $this->gameRepository->findBatchOfGames(0, 8);

        $this->contentStatisticSender->trackVisit($data);

        return $this->render('@App/Common/game_category_content.html.twig', [
            'games' => $games
        ]);
    }

    /**
     * @Route("game/{gameUuid}", name="game_content")
     * @Method("GET")
     * @param Request $request
     * @param string $gameUuid
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function showGameContentAction(Request $request, string $gameUuid = '')
    {
        /** @var Subscription $subscription */
        $subscription = $this->subscriptionExtractor->extractSubscriptionFromSession($request->getSession());
        if (!$subscription
            || !$subscription->isSubscribed()
        ) {
            return new RedirectResponse($this->generateUrl('landing'));
        }

        /** @var Game $game */
        $game = $this->gameRepository->find($gameUuid);

        /** @var GameImage[] $images */
        $images = $this->gameImagesSerializer->serializeGameImages($game->getImages()->getValues());

        $excluded      = $this->excludedGamesProvider->get();
        $similarGames  = $this->gameRepository->getSimilarGames($game, $excluded);
        $aSimilarGames = [];

        foreach ($similarGames ?? [] as $similarGame) {
            $aSimilarGames[] = $this->gameSerializer->serializeGame($similarGame);
        }

        return $this->render('@App/Common/game.html.twig', [
            'game'         => $this->gameSerializer->serializeGame($game),
            'images'       => $images,
            'similarGames' => $aSimilarGames
        ]);
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

        $games = $this->gameRepository->findBatchOfGames((int)$offset, 8);

        $serializedData = [];

        foreach ($games as $game) {
            $serializedData[] = $this->gameSerializer->serializeGame($game);
        }

        return new JsonResponse([
            'games'  => $serializedData,
            'isLast' => count($games) < 8
        ]);
    }

    /**
     * @Route("/games/download", name="download_game")
     * @Method("GET")
     * @param Request $request
     *
     * @return JsonResponse
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function downloadAction(Request $request)
    {
        if (!$id = $request->get('id', null)) {
            throw new BadRequestHttpException('Missing `id` parameter');
        }

        $subscription = $this->subscriptionExtractor->extractSubscriptionFromSession($request->getSession());

        if (!$subscription) {
            throw new BadRequestHttpException('You are not subscribed');
        }

        /** @var GameBuild $gameBuild */
        $gameBuild = $this->gameBuildRepository->findOneBy(['game' => $id]);

        $link = $this->drmApkProvider->getDRMApkUrl($gameBuild);

        $this->contentStatisticSender->trackDownload($subscription, $gameBuild->getGame());

        return new JsonResponse(['url' => $link]);
    }
}