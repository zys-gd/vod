<?php

namespace IdentificationBundle\Carriers\VodafoneEGTpay;

use App\Domain\Constants\ConstBillingCarrierId;
use IdentificationBundle\Identification\Handler\AlreadySubscribedHandler;
use IdentificationBundle\Identification\Service\IdentificationStatus;
use IdentificationBundle\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class VodafoneEGAlreadySubscribedHandler
 */
class VodafoneEGAlreadySubscribedHandler implements AlreadySubscribedHandler
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var IdentificationStatus
     */
    private $identificationStatus;

    /**
     * OrangeEGAlreadySubscribedHandler constructor
     *
     * @param UserRepository $userRepository
     * @param IdentificationStatus $identificationStatus
     */
    public function __construct(
        UserRepository $userRepository,
        IdentificationStatus $identificationStatus
    ) {
        $this->userRepository = $userRepository;
        $this->identificationStatus = $identificationStatus;
    }

    /**
     * @param int $billingCarrierId
     *
     * @return bool
     */
    public function canHandle(int $billingCarrierId): bool
    {
        return $billingCarrierId === ConstBillingCarrierId::VODAFONE_EGYPT_TPAY;
    }

    /**
     * @param Request $request
     *
     * @return void
     */
    public function process(Request $request): void
    {
        $params = $request->query->all();

        if (empty($params['msisdn'])) {
            throw new BadRequestHttpException();
        }

        $user = $this->userRepository->findOneByMsisdn($params['msisdn']);
        $this->identificationStatus->finishIdent($user->getIdentificationToken(), $user);
    }
}