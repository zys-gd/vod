<?php


namespace IdentificationBundle\Identification\Controller;


use IdentificationBundle\BillingFramework\Process\PassthroughProcess;
use IdentificationBundle\Identification\Common\RequestParametersProvider;
use IdentificationBundle\Identification\Service\PassthroughRequestPreparer;
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

    public function __construct(
        PassthroughProcess $passthroughProcess,
        RequestParametersProvider $requestParametersProvider,
        PassthroughRequestPreparer $passthroughRequestPreparer
    )
    {
        $this->passthroughProcess         = $passthroughProcess;
        $this->requestParametersProvider  = $requestParametersProvider;
        $this->passthroughRequestPreparer = $passthroughRequestPreparer;
    }

    /**
     * @Route("/identify-by-passthrough", name="identify_by_passthrough")
     * @param Request $request
     *
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function passthroughAction(Request $request)
    {
        $parameters = $this->passthroughRequestPreparer->getProcessRequestParameters($request);

        $passthrowLink = $this->passthroughProcess->runPassthrough($parameters);

        return new RedirectResponse($passthrowLink);
    }
}