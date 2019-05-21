<?php

namespace IdentificationBundle\Carriers\OrangeEGTpay;

use App\Domain\Constants\ConstBillingCarrierId;
use ExtrasBundle\Utils\LocalExtractor;
use IdentificationBundle\BillingFramework\Process\IdentProcess;
use IdentificationBundle\Entity\CarrierInterface;
use IdentificationBundle\Entity\User;
use IdentificationBundle\Identification\Common\Async\AsyncIdentStarter;
use IdentificationBundle\Identification\Common\RequestParametersProvider;
use IdentificationBundle\Identification\Handler\HasConsentPageFlow;
use IdentificationBundle\Identification\Handler\IdentificationHandlerInterface;
use IdentificationBundle\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class OrangeEGIdentificationHandler
 */
class OrangeEGIdentificationHandler implements IdentificationHandlerInterface, HasConsentPageFlow
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var RequestParametersProvider
     */
    private $parametersProvider;

    /**
     * @var IdentProcess
     */
    private $identProcess;

    /**
     * @var AsyncIdentStarter
     */
    private $asyncIdentStarter;

    /**
     * @var LocalExtractor
     */
    private $localExtractor;

    /**
     * VodafoneEGIdentificationHandler constructor
     *
     * @param UserRepository $userRepository
     * @param RouterInterface $router
     * @param RequestParametersProvider $parametersProvider
     * @param IdentProcess $identProcess
     * @param AsyncIdentStarter $asyncIdentStarter
     * @param LocalExtractor $localExtractor
     */
    public function __construct(
        UserRepository $userRepository,
        RouterInterface $router,
        RequestParametersProvider $parametersProvider,
        IdentProcess $identProcess,
        AsyncIdentStarter $asyncIdentStarter,
        LocalExtractor $localExtractor
    ) {
        $this->userRepository = $userRepository;
        $this->router = $router;
        $this->parametersProvider = $parametersProvider;
        $this->identProcess = $identProcess;
        $this->asyncIdentStarter = $asyncIdentStarter;
        $this->localExtractor = $localExtractor;
    }

    /**
     * @param CarrierInterface $carrier
     *
     * @return bool
     */
    public function canHandle(CarrierInterface $carrier): bool
    {
        return $carrier->getBillingCarrierId() === ConstBillingCarrierId::ORANGE_EGYPT_TPAY;
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    public function getAdditionalIdentificationParams(Request $request): array
    {
        return ['lang' => $this->localExtractor->getLocal()];
    }

    /**
     * @param string $msisdn
     *
     * @return User|null
     */
    public function getExistingUser(string $msisdn): ?User
    {
        return $this->userRepository->findOneByMsisdn($msisdn);
    }

    /**
     * @param Request $request
     * @param CarrierInterface $carrier
     * @param string $token
     *
     * @return RedirectResponse
     */
    public function onProcess(Request $request, CarrierInterface $carrier, string $token): RedirectResponse
    {
        $additionalParams = $this->getAdditionalIdentificationParams($request);
        $successUrl = $this->router->generate('subscription.consent_page_subscribe', [], RouterInterface::ABSOLUTE_URL);
        $waitPageUrl = $this->router->generate('wait_for_callback', ['successUrl' => $successUrl], RouterInterface::ABSOLUTE_URL);

        $parameters = $this->parametersProvider->prepareRequestParameters(
            $token,
            $carrier->getBillingCarrierId(),
            $request->getClientIp(),
            $waitPageUrl,
            $request->headers->all(),
            $additionalParams
        );

        $processResult = $this->identProcess->doIdent($parameters);

        return $this->asyncIdentStarter->start($processResult, $token);
    }
}