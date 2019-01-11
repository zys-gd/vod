<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 08.01.19
 * Time: 18:29
 */

namespace IdentificationBundle\Service\Action\Identification;


use IdentificationBundle\Repository\CarrierRepositoryInterface;
use IdentificationBundle\Service\Action\Identification\Common\CommonFlowHandler;
use IdentificationBundle\Service\Action\Identification\Common\Exception\MissingIdentificationDataException;
use IdentificationBundle\Service\Action\Identification\Common\HeaderEnrichmentHandler;
use IdentificationBundle\Service\Action\Identification\Common\IdentificationFlowDataExtractor;
use IdentificationBundle\Service\Action\Identification\DTO\IdentifyResult;
use IdentificationBundle\Service\Action\Identification\Handler\HasCommonFlow;
use IdentificationBundle\Service\Action\Identification\Handler\HasCustomFlow;
use IdentificationBundle\Service\Action\Identification\Handler\HasHeaderEnrichment;
use IdentificationBundle\Service\Action\Identification\Handler\IdentificationHandlerProvider;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Identifier
{
    /**
     * @var IdentificationHandlerProvider
     */
    private $handlerProvider;
    /**
     * @var CarrierRepositoryInterface
     */
    private $carrierRepository;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var CommonFlowHandler
     */
    private $commonFlowHandler;
    /**
     * @var HeaderEnrichmentHandler
     */
    private $headerEnrichmentHandler;


    /**
     * Identifier constructor.
     * @param IdentificationHandlerProvider $handlerProvider
     * @param CarrierRepositoryInterface    $carrierRepository
     * @param LoggerInterface               $logger
     * @param CommonFlowHandler             $commonFlowHandler
     * @param HeaderEnrichmentHandler       $headerEnrichmentHandler
     */
    public function __construct(
        IdentificationHandlerProvider $handlerProvider,
        CarrierRepositoryInterface $carrierRepository,
        LoggerInterface $logger,
        CommonFlowHandler $commonFlowHandler,
        HeaderEnrichmentHandler $headerEnrichmentHandler
    )
    {
        $this->handlerProvider         = $handlerProvider;
        $this->carrierRepository       = $carrierRepository;
        $this->logger                  = $logger;
        $this->commonFlowHandler       = $commonFlowHandler;
        $this->headerEnrichmentHandler = $headerEnrichmentHandler;
    }

    public function identify(int $carrierBillingId, Request $request, string $token, SessionInterface $session): IdentifyResult
    {
        $carrier = $this->carrierRepository->findOneByBillingId($carrierBillingId);

        $handler = $this->handlerProvider->get($carrier);
        $this->logger->debug('Resolved handler for identification', [
            'className' => get_class($handler),
            'carrierId' => $carrierBillingId
        ]);

        if ($handler instanceof HasHeaderEnrichment) {
            $this->headerEnrichmentHandler->process($request, $handler, $carrier, $token);
            $this->storeIdentificationData($session, $token);

            return new IdentifyResult();

        } elseif ($handler instanceof HasCustomFlow) {
            $handler->process($request);
            return new IdentifyResult();

        } else if ($handler instanceof HasCommonFlow) {

            $identificationData = $this->storeIdentificationData($session, $token);
            $response           = $this->commonFlowHandler->process(
                $request,
                $handler,
                $identificationData,
                $carrier
            );
            return new IdentifyResult($response);

        } else {
            throw new \RuntimeException('Handlers for identification should have according interfaces');
        }

    }

    private function storeIdentificationData(SessionInterface $session, string $token): array
    {
        if ($session->has('identification_data')) {
            $identificationData = $session->get('identification_data');
        } else {
            $identificationData = [];
        }
        $identificationData['identification_token'] = $token;
        $session->set('identification_data', $identificationData);

        return $identificationData;
    }

}