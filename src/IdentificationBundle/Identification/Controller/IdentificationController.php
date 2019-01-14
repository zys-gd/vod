<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 10.01.19
 * Time: 17:54
 */

namespace IdentificationBundle\Identification\Controller;


use IdentificationBundle\Identification\DTO\ISPData;
use IdentificationBundle\Identification\Identifier;
use IdentificationBundle\Identification\Service\IdentificationFlowDataExtractor;
use IdentificationBundle\Identification\Service\TokenGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

class IdentificationController extends AbstractController
{
    /**
     * @var Identifier
     */
    private $identifier;
    /**
     * @var TokenGenerator
     */
    private $tokenGenerator;

    /**
     * IdentificationController constructor.
     * @param Identifier     $identifier
     * @param TokenGenerator $generator
     */
    public function __construct(Identifier $identifier, TokenGenerator $generator)
    {
        $this->identifier     = $identifier;
        $this->tokenGenerator = $generator;
    }

    /**
     * @Route("/identify",name="identify")
     * @param Request $request
     * @return Response
     */
    public function identifyAction(Request $request, ISPData $ISPData): Response
    {

        $identData = IdentificationFlowDataExtractor::extractIdentificationData($request->getSession());

        if (isset($identData['identification_token'])) {
            throw new BadRequestHttpException('You are already identified');
        }

        $token  = $this->tokenGenerator->generateToken();
        $result = $this->identifier->identify(
            (int)$ISPData->getCarrierId(),
            $request,
            $token,
            $request->getSession()
        );

        if ($customResponse = $result->getOverridedResponse()) {
            return $customResponse;
        } else {
            return $this->redirectToRoute('landing');
        }
    }
}