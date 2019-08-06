<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 01.08.19
 * Time: 17:48
 */

namespace SubscriptionBundle\Piwik\DTO;


use PiwikBundle\Service\DTO\OrderInformation;

class ConversionEvent
{
    /**
     * @var UserInformation
     */
    private $userInformation;
    /**
     * @var OrderInformation
     */
    private $information;
    /**
     * @var array
     */
    private $additionalData;

    /**
     * ConversionEvent constructor.
     * @param UserInformation  $userInformation
     * @param OrderInformation $information
     * @param array            $additionalData
     */
    public function __construct(UserInformation $userInformation, OrderInformation $information, array $additionalData)
    {
        $this->userInformation = $userInformation;
        $this->information     = $information;
        $this->additionalData  = $additionalData;
    }

    /**
     * @return UserInformation
     */
    public function getUserInformation(): UserInformation
    {
        return $this->userInformation;
    }

    /**
     * @return OrderInformation
     */
    public function getInformation(): OrderInformation
    {
        return $this->information;
    }

    /**
     * @return array
     */
    public function getAdditionalData(): array
    {
        return $this->additionalData;
    }


}