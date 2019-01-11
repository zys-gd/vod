<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 10.01.19
 * Time: 17:54
 */

namespace IdentificationBundle\Controller;


use IdentificationBundle\Service\Action\Identification\Common\IdentificationFlowDataExtractor;
use IdentificationBundle\Service\Action\Identification\Common\TokenGenerator;
use IdentificationBundle\Service\Action\Identification\Identifier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
     * @return void
     */
    public function identifyAction(Request $request): Response
    {
        $data = IdentificationFlowDataExtractor::extractIspDetectionData($request->getSession());

        $token  = $this->tokenGenerator->generateToken();
        $result = $this->identifier->identify(
            $data['carrier_id'],
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