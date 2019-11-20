<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 29.07.19
 * Time: 16:04
 */

namespace SubscriptionBundle\ReportingTool;


use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use IdentificationBundle\Entity\User;
use Psr\Http\Message\StreamInterface;

class ReportingToolRequestSender
{
    private $reportApiHost;
    /**
     * @var string
     */
    private $reportApihHost;
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * ReportingToolRequestSender constructor.
     * @param string          $reportApihHost
     * @param ClientInterface $client
     */
    public function __construct(string $reportApihHost, ClientInterface $client)
    {
        $this->reportApihHost = $reportApihHost;
        $this->client         = $client;
    }


    /**
     * @param User   $user
     * @param string $statsPath
     *
     * @return array
     */
    public function sendRequest(User $user, string $statsPath): array
    {
        $url = $this->reportApiHost . $statsPath . $user->getBillingCarrierId();
        $key = sha1(date("Y") . $user->getIdentifier() . date("d"));

        try {

            $response = $this->client->request('POST', $url, [
                RequestOptions::HEADERS     => ['X-REVERSE-KEY' => $key],
                RequestOptions::FORM_PARAMS => ['msisdn' => $user->getIdentifier()],
            ]);

            $body = $response->getBody();
            if ($body instanceof StreamInterface) {
                $contents = $body->getContents();
                if ($contents) {
                    $data = json_decode($contents, true);
                }
            }
            return $data ?? [];

        } catch (GuzzleException $exception) {
            return [];
        }
    }
}