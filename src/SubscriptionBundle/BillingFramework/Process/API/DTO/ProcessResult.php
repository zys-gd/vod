<?php
/**
 * Created by IntelliJ IDEA.
 * User: bharatm
 * Date: 17/08/17
 * Time: 8:07 PM
 */

namespace SubscriptionBundle\BillingFramework\Process\API\DTO;


use SubscriptionBundle\BillingFramework\Process\RenewProcess;

class ProcessResult
{
    const STATUS_FAILED           = 'failed';
    const STATUS_SUCCESSFUL       = 'successful';
    const STATUS_WAITING_PROVIDER = 'waiting_provider';
    const STATUS_RETRYING         = 'retrying';

    const ERROR_ALREADY_DONE         = 'already_done';
    const ERROR_INTERNAL             = 'internal';
    const ERROR_REJECTED             = 'rejected';
    const ERROR_CONNECTION_ERROR     = 'connection_error';
    const ERROR_NOT_ENOUGH_CREDIT    = 'not_enough_credit';
    const ERROR_CANCELED             = 'canceled';
    const ERROR_TOO_MANY_TRIES       = 'too_many_tries';
    const ERROR_USER_TIMEOUT         = 'user_timeout';
    const ERROR_NOT_FULLY_PAID       = 'not_fully_paid';
    const ERROR_EXPIRED_TIMEOUT      = 'expired_timeout';
    const ERROR_BATCH_LIMIT_EXCEEDED = 'batch_limit_exceeded';

    const PROCESS_SUBTYPE_REDIRECT = 'redirect';
    const PROCESS_SUBTYPE_FINAL    = 'final';
    const PROCESS_SUBTYPE_WAIT     = 'wait';
    const PROCESS_SUBTYPE_PIXEL    = 'pixel';

    const PROCESS_STATUS_FAILED           = 'failed';
    const PROCESS_STATUS_SUCCESSFUL       = 'successful';
    const PROCESS_STATUS_WAITING_PROVIDER = 'waiting_provider';
    const PROCESS_STATUS_RETRYING         = 'retrying';

    /** @var  integer */
    private $id;

    /** @var  string */
    private $subtype;

    /** @var  string|null */
    private $clientId;

    /** @var  string */
    private $url;

    /** @var  string */
    private $status;

    /** @var  string */
    private $error;

    /** @var  string */
    private $chargeValue;

    /** @var int */
    private $chargePaid;

    /** @var  string */
    private $chargeCurrency;

    /** @var  string */
    private $chargeProduct;

    /** @var  integer */
    private $chargeTier;

    /** @var  integer */
    private $chargeStrategy;

    /** @var  string */
    private $type;

    /** @var  string */
    private $message;

    /** @var array */
    private $charge;

    private $provider;

    private $providerId;

    private $providerUser;

    private $clientUser;

    /** @var int */
    private $carrier;

    private $providerFields = [];
    /**
     * @var array
     */
    private $clientFields;

    /**
     * ProcessResult constructor.
     *
     * @param int    $id
     * @param string $subtype
     * @param string $clientId
     * @param string $url
     * @param string $status
     * @param string $error
     * @param string $chargeValue
     * @param string $chargeCurrency
     * @param string $chargeProduct
     * @param int    $chargeTier
     * @param int    $chargeStrategy
     * @param string $type
     * @param string $message
     * @param array  $charge
     * @param null   $provider
     * @param null   $providerId
     * @param null   $providerUser
     * @param null   $clientUser
     * @param array  $providerFields
     * @param array  $clientFields
     * @param int    $chargePaid
     * @param int    $carrier
     */
    public function __construct(
        $id = null,
        $subtype = null,
        $clientId = null,
        $url = null,
        $status = null,
        $error = null,
        $chargeValue = null,
        $chargeCurrency = null,
        $chargeProduct = null,
        $chargeTier = null,
        $chargeStrategy = null,
        $type = null,
        $message = null,
        $charge = null,
        $provider = null,
        $providerId = null,
        $providerUser = null,
        $clientUser = null,
        $providerFields = [],
        $clientFields = [],
        $chargePaid = null,
        int $carrier = null
    )
    {
        $this->id             = $id;
        $this->subtype        = $subtype;
        $this->clientId       = $clientId;
        $this->url            = $url;
        $this->status         = $status;
        $this->error          = $error;
        $this->chargeValue    = $chargeValue;
        $this->chargePaid     = $chargePaid;
        $this->chargeCurrency = $chargeCurrency;
        $this->chargeProduct  = $chargeProduct;
        $this->chargeTier     = $chargeTier;
        $this->chargeStrategy = $chargeStrategy;
        $this->type           = $type;
        $this->message        = $message;
        $this->charge         = $charge;
        $data                 = [];

        // This is done to match the response UI requires as part of subscription request
        $data['data']    = [
            'id'              => $id,
            'subtype'         => $subtype,
            'client_id'       => $clientId,
            'url'             => $url,
            'status'          => $status,
            'error'           => $error,
            'charge_value'    => $chargeValue,
            'charge_paid'     => $chargePaid,
            'charge_currency' => $chargeCurrency,
            'charge_product'  => $chargeProduct,
            'charge_tier'     => $chargeTier,
            'charge_strategy' => $chargeStrategy,
            'response_type'   => $type,
            'message'         => $message,
            'charge'          => $charge
        ];
        $data['message'] = $message;


        $this->provider       = $provider;
        $this->providerId     = $providerId;
        $this->providerUser   = $providerUser;
        $this->clientFields   = $clientFields;
        $this->providerFields = $providerFields;
        $this->clientUser     = $clientUser;
        $this->carrier        = $carrier;
    }

    public function getClientUser()
    {
        return $this->clientUser;
    }

    /**
     * @return int
     */
    public function getCarrier(): ?int
    {
        return $this->carrier;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getSubtype()
    {
        return $this->subtype;
    }

    /**
     * @return string
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }


    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @return string
     */
    public function getChargeValue()
    {
        return $this->chargeValue;
    }

    /**
     * @return string
     */
    public function getChargeCurrency()
    {
        return $this->chargeCurrency;
    }

    /**
     * @return string
     */
    public function getChargeProduct()
    {
        return $this->chargeProduct;
    }

    /**
     * @return int
     */
    public function getChargeTier()
    {
        return $this->chargeTier;
    }


    /**
     * @return int
     */
    public function getChargeStrategy()
    {
        return $this->chargeStrategy;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }


    private function checkError($errorCode)
    {
        return $this->error == $errorCode;
    }

    public function isPutOnHold()
    {

        return ($this->status == self::STATUS_RETRYING &&
            $this->checkError(self::ERROR_NOT_FULLY_PAID) &&
            $this->type == RenewProcess::PROCESS_METHOD_RENEW
        );
    }

    public function isSuccessful()
    {
        // Only checking for status as subtype could be final/provider
        return $this->status == self::PROCESS_STATUS_SUCCESSFUL;
    }

    public function isRedirectRequired()
    {
        return $this->subtype == self::PROCESS_SUBTYPE_REDIRECT && !!isset($this->url);
    }

    public function isFailed()
    {
        return $this->status == self::PROCESS_STATUS_FAILED;
    }

    public function isRejected()
    {
        return $this->status == self::PROCESS_STATUS_FAILED && $this->subtype == self::PROCESS_SUBTYPE_FINAL &&
            $this->checkError(self::ERROR_REJECTED);
    }

    public function getCharge()
    {
        return $this->charge;
    }

    /**
     * @return null
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * @return string|null
     */
    public function getProviderId()
    {
        return $this->providerId;
    }


    public function isFinal(): bool
    {
        return $this->subtype === self::PROCESS_SUBTYPE_FINAL;
    }

    public function isPixel(): bool
    {
        return $this->subtype === self::PROCESS_SUBTYPE_PIXEL;
    }

    public function getProviderUser()
    {
        return $this->providerUser;
    }

    /**
     * @return array
     */
    public function getClientFields(): array
    {
        return (array)$this->clientFields;
    }

    public function isFailedOrSuccessful(): bool
    {
        return $this->isSuccessful() || $this->isFailed();
    }

    /**
     * @return int|null
     */
    public function getChargePaid(): ?int
    {
        return $this->chargePaid;
    }

    /**
     * @return array
     */
    public function getProviderFields(): array
    {
        return $this->providerFields;
    }


}