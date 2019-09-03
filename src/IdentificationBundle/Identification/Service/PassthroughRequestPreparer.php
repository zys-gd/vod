<?php


namespace IdentificationBundle\Identification\Service;


use IdentificationBundle\Identification\Common\RequestParametersProvider;
use IdentificationBundle\Identification\Service\Session\IdentificationFlowDataExtractor;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessRequestParameters;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

class PassthroughRequestPreparer
{
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var RequestParametersProvider
     */
    private $requestParametersProvider;
    /**
     * @var TokenGenerator
     */
    private $generator;

    /**
     * PassthroughRequestPreparer constructor.
     *
     * @param RouterInterface           $router
     * @param RequestParametersProvider $requestParametersProvider
     * @param TokenGenerator            $generator
     */
    public function __construct(
        RouterInterface $router,
        RequestParametersProvider $requestParametersProvider,
        TokenGenerator $generator
    )
    {
        $this->router                    = $router;
        $this->requestParametersProvider = $requestParametersProvider;
        $this->generator                 = $generator;
    }

    public function getProcessRequestParameters(Request $request): ProcessRequestParameters
    {
        $identificationToken = IdentificationFlowDataExtractor::extractIdentificationToken($request->getSession())
                                ?? $this->generator->generateToken();
        $billingCarrierId    = IdentificationFlowDataExtractor::extractBillingCarrierId($request->getSession());
        $successUrl          = $this->router->generate('subscription.subscribe_back', [], RouterInterface::ABSOLUTE_URL);
        $waitPageUrl         = $this
            ->router
            ->generate('wait_for_callback', ['successUrl' => $successUrl], RouterInterface::ABSOLUTE_URL);

        $parameters = $this->requestParametersProvider->prepareRequestParameters(
            $identificationToken,
            $billingCarrierId,
            $request->getClientIp(),
            $waitPageUrl,
            $request->headers->all(),
            []
        );

        return $parameters;
    }
}