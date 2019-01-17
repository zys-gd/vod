<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 15.05.18
 * Time: 10:57
 */

namespace PiwikBundle\Service;


use PiwikBundle\Api\PhpClient;

class PiwikDataSender
{
    /**
     * @var PhpClient
     */
    private $piwikPhpClient;

    /**
     * PiwikEventsConsumer constructor.
     * @param PhpClient $piwikPhpClient
     */
    public function __construct(PhpClient $piwikPhpClient)
    {
        $this->piwikPhpClient = $piwikPhpClient;
    }

    public function sendData(array $data)
    {
        if (!empty($data['piwikData'][0])) {

            $pieces    = explode('&', $data['piwikData'][0]);
            $newpieces = array();
            foreach ($pieces as $k => $piece) {
                if (strpos($piece, 'cip=') === 0) {
                    $rump      = preg_replace('/[^0-9a-zA-Z,\.]/', ',', substr($piece, 4));
                    $subpieces = explode(',', $rump);
                    $ip        = '';
                    foreach ($subpieces as $s => $subpiece) {
                        if ($subpiece !== '') {
                            $ip = $subpiece;
                            break;
                        }
                    }
                    $piece = 'cip=' . $ip;
                }
                $newpieces[] = $piece;
            }
            $newurl               = implode('&', $newpieces) . '&opti=1';
            $data['piwikData'][0] = str_replace(array(' ', "\t", "\n", "\r"), array('%20', '', '', ''), $newurl);
        }
        $result = $this->piwikPhpClient->sendRequestFromQueue($data['piwikData']);

        return $result;
    }
}