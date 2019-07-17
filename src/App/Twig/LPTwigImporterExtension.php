<?php


namespace App\Twig;


use App\CarrierTemplate\TemplateConfigurator;
use IdentificationBundle\Identification\Service\IdentificationFlowDataExtractor;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class LPTwigImporterExtension extends AbstractExtension
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
     * LPTwigImporterExtension constructor.
     * @param SessionInterface     $session
     * @param TemplateConfigurator $templateConfigurator
     */
    public function __construct(SessionInterface $session, TemplateConfigurator $templateConfigurator)
    {
        $this->session = $session;
        $this->templateConfigurator = $templateConfigurator;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('LPImporter', [$this, 'getLPImportPath'])
        ];
    }

    public function getLPImportPath(string $baseFilePath): string
    {
        $ispData            = IdentificationFlowDataExtractor::extractIspDetectionData($this->session);
        $carrierId          = $ispData ? $ispData['carrier_id'] : 0;

        $baseFilePath = str_replace('.html.twig', '', $baseFilePath);
        return $this->templateConfigurator->getTemplate($baseFilePath, $carrierId);
    }
}