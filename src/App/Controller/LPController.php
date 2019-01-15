<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 10.01.19
 * Time: 17:45
 */

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LPController extends AbstractController implements AppControllerInterface
{
    /**
     * @\IdentificationBundle\Controller\Annotation\NoRedirectToWhoops
     * @Route("/lp",name="landing")
     * @param Request $request
     * @return Response
     */
    public function landingPageAction(Request $request)
    {
        $session = $request->getSession();
        return $this->render('@App/Common/landing.html.twig', [
            'isp_detection_data' => $session->get('isp_detection_data'),
        ]);
    }
}