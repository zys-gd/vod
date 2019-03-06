<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 06.03.19
 * Time: 11:13
 */

namespace SubscriptionBundle\Service\Action\Renew\Handler;


class DefaultHandler implements RenewHandlerInterface, HasCommonFlow
{

    public function canHandle(\IdentificationBundle\Entity\CarrierInterface $carrier): bool
    {
        return true;
    }

    public function afterProcess(\SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult $result): void
    {
        // TODO: Implement afterProcess() method.
    }
}