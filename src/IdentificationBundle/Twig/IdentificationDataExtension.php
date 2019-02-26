<?php
/**
 * Created by PhpStorm.
 * User: Yurii Z
 * Date: 25-02-19
 * Time: 18:38
 */

namespace IdentificationBundle\Twig;


use IdentificationBundle\Identification\Service\IdentificationFlowDataExtractor;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class IdentificationDataExtension extends \Twig_Extension
{

    /**
     * @var SessionInterface
     */
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('extractBillingUserId', [$this, 'extractBillingUserId'])
        ];
    }

    /**
     * @return int|null
     */
    public function extractBillingUserId(): ?int
    {
        $ispDetectionData = IdentificationFlowDataExtractor::extractIspDetectionData($this->session);
        return $ispDetectionData['carrier_id'] ?? null;
    }
}