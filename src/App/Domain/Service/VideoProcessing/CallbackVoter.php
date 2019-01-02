<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 25.12.18
 * Time: 14:19
 */

namespace App\Domain\Service\VideoProcessing;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use App\Domain\Service\VideoProcessing\Connectors\CloudinaryConnector;

class CallbackVoter
{
    /**
     * @var CloudinaryConnector
     */
    private $cloudinary;


    /**
     * CallbackVoter constructor.
     */
    public function __construct(CloudinaryConnector $cloudinary)
    {
    $this->cloudinary = $cloudinary;
    }

    public function ensureRequestValidity(Request $request)
    {
        if (!$request->headers->has('x-cld-timestamp')) {
            throw new BadRequestHttpException('`x-cld-timestamp` header is missing');
        }

        if (!$request->headers->has('x-cld-signature')) {
            throw new BadRequestHttpException('`x-cld-signature` header is missing');
        }

        $signature = $this->cloudinary->makeSignature($request->getContent().$request->headers->get('x-cld-timestamp'));

        if($signature !== $request->headers->get('x-cld-signature')){
            throw new BadRequestHttpException('Signature is not valid');
        }
    }
}