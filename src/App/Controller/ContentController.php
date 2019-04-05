<?php
/**
 * Created by PhpStorm.
 * User: Iliya Kobus
 * Date: 1/17/2019
 * Time: 2:19 PM
 */

namespace App\Controller;

use App\CarrierTemplate\TemplateConfigurator;
use App\Domain\Service\FaqProviderService;
use IdentificationBundle\Identification\DTO\ISPData;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContentController extends AbstractController implements AppControllerInterface
{
    /** @var FaqProviderService $faqProviderService */
    protected $faqProviderService;
    /**
     * @var TemplateConfigurator
     */
    private $templateConfigurator;

    /**
     * ContentController constructor.
     *
     * @param FaqProviderService   $faqProviderService
     * @param TemplateConfigurator $templateConfigurator
     */
    public function __construct(FaqProviderService $faqProviderService, TemplateConfigurator $templateConfigurator)
    {
        $this->faqProviderService = $faqProviderService;
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
        return $this->render($template, [
                'questions' => $this->faqProviderService->getSortedQuestions(),
                'answers'   => $this->faqProviderService->getSortedAnswers(),
            ]
        );
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
