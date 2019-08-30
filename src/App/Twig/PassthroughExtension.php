<?php


namespace App\Twig;


use IdentificationBundle\BillingFramework\Process\PassthroughProcess;
use IdentificationBundle\Identification\Common\RequestParametersProvider;
use IdentificationBundle\Identification\Service\PassthroughChecker;
use IdentificationBundle\Identification\Service\Session\IdentificationFlowDataExtractor;
use IdentificationBundle\Repository\CarrierRepositoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class PassthroughExtension extends AbstractExtension
{
    /**
     * @var PassthroughProcess
     */
    private $passthroughProcess;
    /**
     * @var RequestParametersProvider
     */
    private $requestParametersProvider;
    /**
     * @var RequestStack
     */
    private $requestStack;
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var CarrierRepositoryInterface
     */
    private $carrierRepository;
    /**
     * @var PassthroughChecker
     */
    private $passthroughChecker;

    /**
     * PassthroughExtension constructor.
     *
     * @param PassthroughProcess         $passthroughProcess
     * @param RequestParametersProvider  $requestParametersProvider
     * @param RequestStack               $requestStack
     * @param RouterInterface            $router $parameters
     * @param CarrierRepositoryInterface $carrierRepository
     * @param PassthroughChecker         $passthroughChecker
     */
    public function __construct(
        PassthroughProcess $passthroughProcess,
        RequestParametersProvider $requestParametersProvider,
        RequestStack $requestStack,
        RouterInterface $router,
        CarrierRepositoryInterface $carrierRepository,
        PassthroughChecker $passthroughChecker
    )
    {
        $this->passthroughProcess        = $passthroughProcess;
        $this->requestParametersProvider = $requestParametersProvider;
        $this->requestStack              = $requestStack;
        $this->router                    = $router;
        $this->carrierRepository         = $carrierRepository;
        $this->passthroughChecker        = $passthroughChecker;
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('getPassthroughLink', [$this, 'getPassthroughLink']),
            new TwigFunction('isCarrierPassthrough', [$this, 'isCarrierPassthrough']),
        ];
    }

    /**
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getPassthroughLink(): string
    {
        $identificationToken = IdentificationFlowDataExtractor::extractIdentificationToken($this->requestStack->getCurrentRequest()->getSession());
        $billingCarrierId    = IdentificationFlowDataExtractor::extractBillingCarrierId($this->requestStack->getCurrentRequest()->getSession());
        $successUrl          = $this->router->generate('subscription.subscribe_back', [], RouterInterface::ABSOLUTE_URL);
        $waitPageUrl         = $this
            ->router
            ->generate('wait_for_callback', ['successUrl' => $successUrl], RouterInterface::ABSOLUTE_URL);

        $parameters = $this->requestParametersProvider->prepareRequestParameters(
            $identificationToken,
            $billingCarrierId,
            $this->requestStack->getCurrentRequest()->getClientIp(),
            $waitPageUrl,
            $this->requestStack->getCurrentRequest()->headers->all(),
            []
        );

        return $this->passthroughProcess->runPassthrough($parameters);
    }

    /**
     * @return bool
     */
    public function isCarrierPassthrough(): bool
    {
        $ispDetectionData = IdentificationFlowDataExtractor::extractIspDetectionData($this->requestStack->getCurrentRequest()->getSession());
        try {
            $billingCarrierId = (int)$ispDetectionData['carrier_id'];
            $carrier          = $this->carrierRepository->findOneByBillingId($billingCarrierId);
            return $this->passthroughChecker->isCarrierPassthrough($carrier);
        } catch (\Throwable $e) {
            return false;
        }
    }
}