<?php

namespace App\Controller;

use App\Domain\Entity\UploadedVideo;
use App\Domain\Service\VideoProcessing\Callback\EagerType;
use App\Domain\Service\VideoProcessing\CallbackVoter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class APIController extends Controller
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
            return new JsonResponse([], 400);
        }

        $this->eagerType->handle($request);

        return new JsonResponse();
    }
}
