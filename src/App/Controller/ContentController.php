<?php
/**
 * Created by PhpStorm.
 * User: Iliya Kobus
 * Date: 1/17/2019
 * Time: 2:19 PM
 */

namespace App\Controller;

use App\CarrierTemplate\TemplateConfigurator;
use IdentificationBundle\Identification\DTO\ISPData;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \App\Exception\WrongTranslationRecordType
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
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function termsAndConditionsAction(ISPData $data)
    {
        $template = $this->templateConfigurator->getTemplate('terms_and_conditions', $data->getCarrierId());
        return $this->render($template);
    }
}
