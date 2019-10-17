<?php
/**
 * Created by PhpStorm.
 * User: Iliya Kobus
 * Date: 1/17/2019
 * Time: 2:19 PM
 */

namespace App\Controller;

use CommonDataBundle\Service\TemplateConfigurator\Exception\TemplateNotFoundException;
use CommonDataBundle\Service\TemplateConfigurator\TemplateConfigurator;
use IdentificationBundle\Identification\DTO\ISPData;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContentController extends AbstractController implements AppControllerInterface
{
    /**
     * @var TemplateConfigurator
     */
    private $templateConfigurator;

    /**
     * ContentController constructor.
     *
     * @param TemplateConfigurator $templateConfigurator
     */
    public function __construct(TemplateConfigurator $templateConfigurator)
    {
        $this->templateConfigurator = $templateConfigurator;
    }

    /**
     * @Route("/faq",name="faq")
     * @param ISPData $data
     *
     * @return Response
     *
     * @throws TemplateNotFoundException
     */
    public function faqAction(ISPData $data)
    {
        $template = $this->templateConfigurator->getTemplate('faq', $data->getCarrierId());

        return $this->render($template);
    }

    /**
     * @Route("/terms-and-conditions",name="terms_and_conditions")
     * @param ISPData $data
     *
     * @return Response
     *
     * @throws TemplateNotFoundException
     */
    public function termsAndConditionsAction(ISPData $data)
    {
        $template = $this->templateConfigurator->getTemplate('terms_and_conditions', $data->getCarrierId());

        return $this->render($template);
    }

    /**
     * @Route("/about-us", name="about_us")
     * @param ISPData $data
     *
     * @return Response
     *
     * @throws TemplateNotFoundException
     */
    public function aboutUsAction(ISPData $data)
    {
        $template = $this->templateConfigurator->getTemplate('about_us', $data->getCarrierId());

        return $this->render($template);
    }
}
