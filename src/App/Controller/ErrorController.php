<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 11.01.19
 * Time: 12:17
 */

namespace App\Controller;


use IdentificationBundle\Identification\Service\CarrierSelector;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ErrorController extends AbstractController
{
    /**
     * @var CarrierSelector
     */
    private $carrierSelector;

    /**
     * ErrorController constructor.
     */
    public function __construct(CarrierSelector $carrierSelector)
    {
        $this->carrierSelector = $carrierSelector;
    }


    /**
     * @Route("/whoops",name="mobile_identification")
     * @param Request $request
     * @return Response
     */
    public function wrongCarrierAction(Request $request)
    {
        $var = $request->request->get('carrier');
        if ($var) {
            $this->carrierSelector->selectCarrier((int)$var);
        }

        return $this->render('@App/Error/wrong_carrier.html.twig');
    }
}