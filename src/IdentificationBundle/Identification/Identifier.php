<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 08.01.19
 * Time: 18:29
 */

namespace IdentificationBundle\Identification;


use IdentificationBundle\Identification\Common\CommonFlowHandler;
use IdentificationBundle\Identification\Common\HeaderEnrichmentHandler;
use IdentificationBundle\Identification\DTO\IdentifyResult;
use IdentificationBundle\Identification\Handler\HasCommonFlow;
use IdentificationBundle\Identification\Handler\HasCustomFlow;
use IdentificationBundle\Identification\Handler\HasHeaderEnrichment;
use IdentificationBundle\Identification\Handler\IdentificationHandlerProvider;
use IdentificationBundle\Identification\Service\IdentificationDataStorage;
use IdentificationBundle\Repository\CarrierRepositoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
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
     * @var \IdentificationBundle\Identification\Common\HeaderEnrichmentHandler
     */
    private $headerEnrichmentHandler;


    /**
     * Identifier constructor.
     * @param IdentificationHandlerProvider                                       $handlerProvider
     * @param CarrierRepositoryInterface                                          $carrierRepository
     * @param LoggerInterface                                                     $logger
     * @param CommonFlowHandler                                                   $commonFlowHandler
     * @param \IdentificationBundle\Identification\Common\HeaderEnrichmentHandler $headerEnrichmentHandler
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

    public function identify(int $carrierBillingId, Request $request, string $token): IdentifyResult
    {
        $carrier = $this->carrierRepository->findOneByBillingId($carrierBillingId);

        $handler = $this->handlerProvider->get($carrier);
        $this->logger->debug('Resolved handler for identification', [
            'className' => get_class($handler),
            'carrierId' => $carrierBillingId
        ]);

        if ($handler instanceof HasHeaderEnrichment) {
            $this->headerEnrichmentHandler->process($request, $handler, $carrier, $token);
            return new IdentifyResult();

        } elseif ($handler instanceof HasCustomFlow) {
            $handler->process($request);
            return new IdentifyResult();

        } else if ($handler instanceof HasCommonFlow) {
            $response = $this->commonFlowHandler->process($request, $handler, $token, $carrier);
            return new IdentifyResult($response);

        } else {
            throw new \RuntimeException('Handlers for identification should have according interfaces');
        }

    }

}