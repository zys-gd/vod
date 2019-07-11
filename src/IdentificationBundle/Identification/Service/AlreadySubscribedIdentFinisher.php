<?php

namespace IdentificationBundle\Identification\Service;

use IdentificationBundle\Identification\Handler\AlreadySubscribedHandlerProvider;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AlreadySubscribedIdentFinisher
 */
class AlreadySubscribedIdentFinisher
{
    /**
     * @var IdentificationStatus
     */
    private $identificationStatus;

    /**
     * @var AlreadySubscribedHandlerProvider
     */
    private $handlerProvider;

    /**
     * AlreadySubscribedIdentFinisher constructor
     *
     * @param IdentificationStatus $identificationStatus
     * @param AlreadySubscribedHandlerProvider $handlerProvider
     */
    public function __construct(
        IdentificationStatus $identificationStatus,
        AlreadySubscribedHandlerProvider $handlerProvider
    ) {
        $this->identificationStatus = $identificationStatus;
        $this->handlerProvider = $handlerProvider;
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    public function needToHandle(Request $request)
    {
        $params = $request->query->all();
        $errorHandler = empty($params['err_handle']) ? null : $params['err_handle'];

        return $errorHandler === 'already_subscribed' && !$this->identificationStatus->isIdentified();
    }

    /**
     * @param Request $request
     *
     * @return void
     */
    public function tryToIdentify(Request $request): void
    {
        if (!$ispData = IdentificationFlowDataExtractor::extractIspDetectionData($request->getSession())) {
            return;
        }

        if (!$handler = $this->handlerProvider->get($ispData['carrier_id'])) {
            return;
        }

        try {
            $handler->process($request);
        } catch (\Exception $exception) {
            return ;
        }
    }
}