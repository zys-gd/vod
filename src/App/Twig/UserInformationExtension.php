<?php


namespace App\Twig;


use IdentificationBundle\Identification\DTO\IdentificationData;
use IdentificationBundle\Identification\Handler\HasHeaderEnrichment;
use IdentificationBundle\Identification\Handler\IdentificationHandlerProvider;
use IdentificationBundle\Identification\Service\Session\IdentificationFlowDataExtractor;
use IdentificationBundle\Repository\CarrierRepositoryInterface;
use IdentificationBundle\User\Service\UserExtractor;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class UserInformationExtension extends AbstractExtension
{
    /**
     * @var UserExtractor
     */
    private $userExtractor;
    /**
     * @var IdentificationHandlerProvider
     */
    private $handlerProvider;
    /**
     * @var CarrierRepositoryInterface
     */
    private $carrierRepository;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * UserInformationExtension constructor.
     *
     * @param RequestStack                  $requestStack
     * @param UserExtractor                 $userExtractor
     * @param IdentificationHandlerProvider $handlerProvider
     * @param CarrierRepositoryInterface    $carrierRepository
     */
    public function __construct(RequestStack $requestStack,
        UserExtractor $userExtractor,
        IdentificationHandlerProvider $handlerProvider,
        CarrierRepositoryInterface $carrierRepository)
    {
        $this->userExtractor     = $userExtractor;
        $this->handlerProvider   = $handlerProvider;
        $this->carrierRepository = $carrierRepository;
        $this->requestStack      = $requestStack;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('getUserIdentifier', [$this, 'getUserIdentifier']),

        ];
    }

    /**
     * @return string|null
     */
    public function getUserIdentifier(): ?string
    {
        $identificationToken = IdentificationFlowDataExtractor::extractIdentificationToken($this->requestStack->getCurrentRequest()->getSession());
        $billingCarrierId    = IdentificationFlowDataExtractor::extractBillingCarrierId($this->requestStack->getCurrentRequest()->getSession());

        try{
            $carrier = $this->carrierRepository->findOneByBillingId($billingCarrierId);
            $handler = $this->handlerProvider->get($carrier);
            if ($handler instanceof HasHeaderEnrichment) {
                return $handler->getMsisdn($this->requestStack->getCurrentRequest());
            }
        } catch (\Throwable $e) {}

        try {
            $identificationData = new IdentificationData($identificationToken);
            $user               = $this->userExtractor->getUserByIdentificationData($identificationData);
            return $user->getIdentifier();
        } catch (\Throwable $e) {
            return null;
        }
    }
}