<?php


namespace App\Twig;


use CommonDataBundle\Service\TemplateConfigurator\TemplateConfigurator;
use IdentificationBundle\Identification\Service\Session\IdentificationFlowDataExtractor;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TemplateImporterExtension extends AbstractExtension
{
    /**
     * @var SessionInterface
     */
    private $session;
    /**
     * @var TemplateConfigurator
     */
    private $templateConfigurator;

    /**
     * TemplateImporterExtension constructor.
     *
     * @param SessionInterface     $session
     * @param TemplateConfigurator $templateConfigurator
     */
    public function __construct(SessionInterface $session, TemplateConfigurator $templateConfigurator)
    {
        $this->session              = $session;
        $this->templateConfigurator = $templateConfigurator;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('importTemplate', [$this, 'importTemplate']),
        ];
    }

    /**
     * @param string $baseFilePath
     *
     * @return string
     */
    public function importTemplate(string $baseFilePath): string
    {
        $billingCarrierId = (int)IdentificationFlowDataExtractor::extractBillingCarrierId($this->session);

        $urlParts = explode('/', $baseFilePath);

        $templateName = str_replace('.html.twig', '', array_pop($urlParts));
        $templatePath = implode('/', $urlParts);

        return $this->templateConfigurator->getTemplate($templateName, $billingCarrierId, "@App/$templatePath");
    }
}