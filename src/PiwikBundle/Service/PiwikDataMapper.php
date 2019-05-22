<?php


namespace PiwikBundle\Service;


use PiwikBundle\Api\ClientAbstract;

class PiwikDataMapper
{
    protected $customVars = [
        'operator'          => [
            'id'   => 6,
            'name' => 'operator',
        ],
        'affiliate'         => [
            'id'   => 7,
            'name' => 'affiliate',
        ],
        'publisher'         => [
            'id'   => 8,
            'name' => 'publisher',
        ],
        'subscription-type' => [
            'id'   => 10,
            'name' => 'subscription-type',
        ],
        'aff_publisher'     => [
            'id'   => 9,
            'name' => 'aff_publisher',
        ],

        'msisdn'               => [
            'id'   => 1,
            'name' => 'msisdn',
        ],
        'connection'           => [
            'id'   => 2,
            'name' => 'connection',
        ],
        'conversion_mode'      => [
            'id'   => 3,
            'name' => 'conversion_mode',
        ],
        'currency'             => [
            'id'   => 4,
            'name' => 'currency',
        ],
        'provider'             => [
            'id'   => 5,
            'name' => 'provider',
        ],
        'device_screen_height' => [
            'id'   => 11,
            'name' => 'device_screen_height',
        ],
        'device_screen_width'  => [
            'id'   => 12,
            'name' => 'device_screen_width',
        ],
        'game_name'            => [
            'id'   => 13,
            'name' => 'game_name'
        ],
        'game_uuid'            => [
            'id'   => 14,
            'name' => 'game_uuid'
        ],
        'video_name'           => [
            'id'   => 15,
            'name' => 'video_name'
        ],
        'video_uuid'           => [
            'id'   => 16,
            'name' => 'video_uuid'
        ]
    ];

    /**
     * @var ClientAbstract
     */
    private $piwikClient;

    public function __construct(ClientAbstract $piwikClient)
    {
        $this->piwikClient = $piwikClient;
    }

    /**
     * @param        $key
     * @param        $value
     * @param string $scope
     *
     * @throws \Exception
     */
    protected function addVariable($key, $value, $scope = 'visit')
    {
        try {
            $this->piwikClient->setCustomVariable($this->customVars[$key]['id'], $this->customVars[$key]['name'], $value, $scope);
        } catch (\Throwable $e) {
        }
    }

    /**
     * @param DTO\PiwikDTO $piwikDTO
     *
     * @throws \Exception
     */
    public function mapData(DTO\PiwikDTO $piwikDTO)
    {
        if ($piwikDTO->getOperator()) {
            $this->addVariable('operator', $piwikDTO->getOperator());
        }
        if ($piwikDTO->getConnection()) {
            $this->addVariable('connection', $piwikDTO->getConnection());
        }
        if ($piwikDTO->getCountry()) {
            $this->piwikClient->setCountry($piwikDTO->getCountry());
        }
        if ($piwikDTO->getIp()) {
            $this->piwikClient->setIp($piwikDTO->getIp());
        }
        if ($piwikDTO->getMsisdn()) {
            $this->addVariable('msisdn', $piwikDTO->getMsisdn());
        }
        if ($piwikDTO->getAffiliate()) {
            $this->addVariable('affiliate', $piwikDTO->getAffiliate());
        }
    }

    /**
     * @param array $additionData
     * @param bool  $clear
     *
     * @throws \Exception
     */
    public function mapAdditionalData(array $additionData, bool $clear = false)
    {
        $clear && $this->piwikClient->clearCustomVariables();
        foreach ($additionData as $key => $value) {
            $this->addVariable($key, $value);
        }
    }
}