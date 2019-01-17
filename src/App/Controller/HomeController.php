<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 08.01.19
 * Time: 16:45
 */

namespace App\Controller;


use IdentificationBundle\Controller\ControllerWithIdentification;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends AbstractController implements AppControllerInterface, ControllerWithIdentification
{

    /**
     * @Route("/",name="index")
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request)
    {

        return $this->render('@App/Common/home.html.twig', [
            'identificationData' => $request->getSession()->get('identification_data'),
        ]);
    }
}