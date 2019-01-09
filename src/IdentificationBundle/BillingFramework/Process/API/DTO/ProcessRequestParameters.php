<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 13.11.18
 * Time: 10:55
 */

namespace IdentificationBundle\BillingFramework\Process\API\DTO;


class ProcessRequestParameters
{

    /**
     * The URL where each change of the process will be notified. The notification will come only if the process
     * status is one of 'successful','failed','expired' and the process subtype is 'redirect'
     */
    public $client;

    public $listener;


    public $userHeaders;

    // The end user MSISDN or custom provider-side userid.
    public $clientUser;

    // The end user IP Address
    public $userIp;

    // The carrier ID of the end user.
    public $carrier;

    // The unique transaction id for this process, store-side. Duplicate values will not be rejected.
    public $clientId;
    /**
     *  The store URL where the end user will finish if this request ends in a redirect-type response.
     */
    public $redirectUrl;

    /**
     * The URL where the step to follow will be posted, in case this request ends in a 'wait' status.group.
     * If not specified, it will be copied from the listener parameter.
     */
    public $listenerWait;

    // The unique product identifier. Used only for tracking purposes.
    public $chargeProduct;

    //The pricing tier to be used.
    public $chargeTier;

    // The ID for the strategy to use. If this strategy is not defined for the requested carrier and tier,
    // the default strategy will be used. To see all strategies
    public $chargeStrategy;

    public $additionalData = [];
}