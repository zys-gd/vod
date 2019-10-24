<?php


namespace IdentificationBundle\Identification\Controller;


use IdentificationBundle\BillingFramework\Process\PassthroughProcess;
use IdentificationBundle\Identification\Common\RequestParametersProvider;
use IdentificationBundle\Identification\Service\PassthroughRequestPreparer;
use SubscriptionBundle\Subscription\Common\RouteProvider;
use SubscriptionBundle\Subscription\Common\SubscriptionExtractor;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PassthroughController extends AbstractController
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
     * @var PassthroughRequestPreparer
     */
    private $passthroughRequestPreparer;

    /**
     * @var SubscriptionExtractor
     */
    private $subscriptionExtractor;
    /**
     * @var RouteProvider
     */
    private $routeProvider;

    /**
     * PassthroughController constructor.
     *
     * @param PassthroughProcess         $passthroughProcess
     * @param RequestParametersProvider  $requestParametersProvider
     * @param PassthroughRequestPreparer $passthroughRequestPreparer
     * @param SubscriptionExtractor      $subscriptionExtractor
     * @param RouteProvider              $routeProvider
     */
    public function __construct(
        PassthroughProcess $passthroughProcess,
        RequestParametersProvider $requestParametersProvider,
        PassthroughRequestPreparer $passthroughRequestPreparer,
        SubscriptionExtractor $subscriptionExtractor,
        RouteProvider $routeProvider
    )
    {
        $this->passthroughProcess         = $passthroughProcess;
        $this->requestParametersProvider  = $requestParametersProvider;
        $this->passthroughRequestPreparer = $passthroughRequestPreparer;
        $this->subscriptionExtractor      = $subscriptionExtractor;
        $this->routeProvider              = $routeProvider;
    }

    /**
     * @Route("/identify-by-passthrough", name="identify_by_passthrough")
     * @param Request $request
     *
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function passthroughAction(Request $request)
    {
        if ($subscription = $this->subscriptionExtractor->extractSubscriptionFromSession($request->getSession())) {
            $subPack = $subscription->getSubscriptionPack();
            if ($subscription->isUnsubscribed() && !$subPack->isResubAllowed()) {
                return new RedirectResponse($this->routeProvider->getResubNotAllowedRoute());
            }
        }

        $parameters = $this->passthroughRequestPreparer->getProcessRequestParameters($request);

        $passthrowLink = $this->passthroughProcess->runPassthrough($parameters);

        return new RedirectResponse($passthrowLink);
    }
}