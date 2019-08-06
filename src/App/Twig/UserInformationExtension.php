<?php


namespace App\Twig;


use IdentificationBundle\Identification\DTO\IdentificationData;
use IdentificationBundle\Identification\Service\Session\IdentificationFlowDataExtractor;
use SubscriptionBundle\Service\UserExtractor;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class UserInformationExtension extends AbstractExtension
{

    /**
     * @var UserExtractor
     */
    private $userExtractor;
    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * UserInformationExtension constructor.
     *
     * @param SessionInterface $session
     * @param UserExtractor    $userExtractor
     */
    public function __construct(SessionInterface $session, UserExtractor $userExtractor)
    {
        $this->userExtractor = $userExtractor;
        $this->session       = $session;
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
        if (!$identificationToken = IdentificationFlowDataExtractor::extractIdentificationToken($this->session)) {
            throw new BadRequestHttpException('Identification data is not found');
        }

        $identificationData = new IdentificationData($identificationToken);
        $user = $this->userExtractor->getUserByIdentificationData($identificationData);
        return $user->getIdentifier();
    }
}