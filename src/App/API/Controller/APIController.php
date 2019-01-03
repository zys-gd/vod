<?php

namespace App\API\Controller;

use App\API\Utils\ResponseMaker;
use App\API\Controller\APIControllerInterface;
use App\Domain\Entity\UploadedVideo;
use App\Domain\Service\VideoProcessing\Callback\EagerType;
use App\Domain\Service\VideoProcessing\CallbackVoter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/v0")
 */
class APIController implements APIControllerInterface
{
    /**
     * @var CallbackVoter
     */
    private $callbackVoter;
    /**
     * @var EagerType
     */
    private $eagerType;

    /**
     * APIController constructor.
     * @param CallbackVoter $callbackVoter
     * @param EagerType     $eagerType
     */
    public function __construct(CallbackVoter $callbackVoter, EagerType $eagerType)
    {
        $this->callbackVoter = $callbackVoter;
        $this->eagerType     = $eagerType;
    }


    /**
     * @Route("/listen",name="vod_listen")
     */
    public function listenAction(Request $request)
    {
        $this->callbackVoter->ensureRequestValidity($request);


        if (!$this->eagerType->isSupports($request)) {
            throw new BadRequestHttpException('Not supported API notification');
        }

        $this->eagerType->handle($request);

        return ResponseMaker::makeSuccessResponse();
    }
}
