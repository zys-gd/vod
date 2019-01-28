<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 14.01.19
 * Time: 13:02
 */

namespace IdentificationBundle\Identification\Service;


use Symfony\Component\HttpFoundation\Session\SessionInterface;

class IdentificationDataStorage
{
    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * IdentificationDataStorage constructor.
     */
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function readValue(string $key)
    {
        return $this->session->get("storage[$key]", '');
    }

    public function storeValue(string $key, $value)
    {
        $this->session->set("storage[$key]", $value);
    }

    public function storeOperationResult(string $key, $result)
    {
        $this->session->set("results[$key]", serialize($result));
    }

    public function readPreviousOperationResult(string $key)
    {
        return unserialize($this->session->get("results[$key]"));
    }

    public function cleanPreviousOperationResult(string $key): void
    {
        $this->session->remove("results[$key]");
    }

    public function storeIdentificationToken(string $token)
    {
        $identificationData = $this->readIdentificationData();

        $identificationData['identification_token'] = $token;

        $this->setIdentificationData($identificationData);
    }

    /**
     * @return array
     */
    public function readIdentificationData(): array
    {
        if ($this->session->has('identification_data')) {
            $identificationData = $this->session->get('identification_data');
        } else {
            $identificationData = [];
        }
        return $identificationData;
    }

    /**
     * @param $identificationData
     */
    private function setIdentificationData(array $identificationData): void
    {
        $this->session->set('identification_data', $identificationData);
    }

    public function storeCarrierId(int $carrierId): void
    {
        $this->session->set('isp_detection_data', ['carrier_id' => $carrierId]);
    }
}