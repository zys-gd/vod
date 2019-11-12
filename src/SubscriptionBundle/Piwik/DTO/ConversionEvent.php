<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 01.08.19
 * Time: 17:48
 */

namespace SubscriptionBundle\Piwik\DTO;


class ConversionEvent
{
    /**
     * @var UserInformation
     */
    private $userInformation;
    /**
     * @var OrderInformation
     */
    private $orderInformation;
    /**
     * @var array
     */
    private $additionalData;
    /**
     * @var string
     */
    private $conversionName;

    /**
     * ConversionEvent constructor.
     * @param UserInformation  $userInformation
     * @param string           $conversionName
     * @param OrderInformation $orderInformation
     * @param array            $additionalData
     */
    public function __construct(
        UserInformation $userInformation,
        string $conversionName = '',
        OrderInformation $orderInformation = null,
        array $additionalData = []
    )
    {
        $this->userInformation  = $userInformation;
        $this->orderInformation = $orderInformation;

        $this->ensureAdditionalDataIsCorrect($additionalData);

        $this->additionalData = $additionalData;

        if (!$conversionName && $orderInformation) {
            $this->conversionName = $orderInformation->getOrderId();
        } else {
            $this->conversionName = (string)$conversionName;
        }
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
    public function getOrderInformation(): ?OrderInformation
    {
        return $this->orderInformation;
    }

    /**
     * @return array
     */
    public function getAdditionalData(): array
    {
        return $this->additionalData;
    }

    private function ensureAdditionalDataIsCorrect(array $additionalData): void
    {
        // Reporting tool compatibility.
        $exception = new \RuntimeException(sprintf('Additional data should be in following format: %s', json_encode([
            '10' => ['PROPERTY_NAME', 'PROPERTY_VALUE']
        ])));

        foreach ($additionalData as $k => $element) {

            if (!is_numeric($k)) {
                throw $exception;
            }

            if (!isset($element[0], $element[1])) {
                throw $exception;
            }
        }
    }

    /**
     * @return string
     */
    public function getConversionName(): string
    {
        return $this->conversionName;
    }


}