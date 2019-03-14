<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 05.02.19
 * Time: 11:56
 */

namespace App\Domain\Service;

use App\Domain\Service\Campaign\CampaignService;
use App\Domain\Service\Email\EmailComposer;
use App\Domain\Repository\CampaignRepository;
use App\Domain\Entity\Affiliate;
use App\Domain\Entity\Carrier;
use CountryCarrierDetectionBundle\Service\MaxMindIpInfo;
use DeviceDetectionBundle\Service\Device;
use IdentificationBundle\Entity\User;
use SubscriptionBundle\Entity\Affiliate\AffiliateLog;
use SubscriptionBundle\Repository\Affiliate\AffiliateLogRepository;
use SubscriptionBundle\Service\SubscriptionService;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use BeSimple\I18nRoutingBundle\Routing\Router;


class ContactUsMessageSender
{
    /**
     * @var \App\Domain\Service\Email\EmailComposer $composer
     */
    private $composer;
    /**
     * @var \Twig_Environment
     */
    private $templating;
    /**
     * @var \Swift_Mailer
     */
    private $mailer;
    /**
     * @var SubscriptionService
     */
    private $subscriptionService;
    /**
     * @var CampaignService
     */
    private $campaignService;
    /**
     * @var AffiliateLogRepository
     */
    private $affLogRepository;
    /**
     * @var CampaignRepository
     */
    private $campaignRepository;
    /**
     * @var Device
     */
    private $deviceDetectionService;
    /**
     * @var MaxMindIpInfo
     *
     */
    private $maxMindIpInfoService;

    /** @var array */
    private $data = [];

    /** @var array */
    private $campaignData = [];

    /**
     * ContactUsMessageSender constructor.
     * @param EmailComposer $composer
     * @param \Twig_Environment $templating
     * @param \Swift_Mailer $mailer
     * @param SubscriptionService $subscriptionService
     * @param CampaignService $campaignService
     * @param AffiliateLogRepository $affLogRepository
     * @param CampaignRepository $campaignRepository
     * @param Device $deviceDetectionService
     * @param MaxMindIpInfo $maxMindIpInfoService
     */

    public function __construct(EmailComposer $composer,
                                \Twig_Environment $templating,
                                \Swift_Mailer $mailer,
                                SubscriptionService $subscriptionService,
                                CampaignService $campaignService,
                                AffiliateLogRepository $affLogRepository,
                                CampaignRepository $campaignRepository,
                                Device $deviceDetectionService,
                                MaxMindIpInfo $maxMindIpInfoService
    )
    {

        $this->composer = $composer;
        $this->templating = $templating;
        $this->mailer = $mailer;
        $this->subscriptionService = $subscriptionService;
        $this->campaignService = $campaignService;
        $this->affLogRepository = $affLogRepository;
        $this->campaignRepository = $campaignRepository;
        $this->deviceDetectionService = $deviceDetectionService;
        $this->maxMindIpInfoService = $maxMindIpInfoService;
        $this->campaignData = $this->campaignService->getCampaignDataFromSession();
        $this->setupDefaultData();
    }


    public function generateMailData(Carrier $carrier, User $user = null, string $email, string $comment)
    {
        if (null !== $user) {
            $this->getDataByUser($user, $email, $comment);
        } else {
            $this->getDataByCarrier($carrier);
        }
        $this->addCampaignData();

        return $this;
    }

    public function add(string $key, $data)
    {
        $this->data[$key] = $data;

        return $this;
    }

    /**
     * @param User $User
     */
    private function getDataByUser(User $user, string $email, string $comment)
    {
        $this->add('email', $email);
        $this->add('comment', $comment);
        $this->add('user', $user);
        $this->add('subscription', $this->subscriptionService->getSubscription($user));
        $this->add('subscriptionProducts', $this->subscriptionService->getSubscriptionProducts($user));
        $this->add('gameDownloadCount', count($this->data['subscriptionProducts']));
        $this->add('country', $user->getCountry());
        $this->add('carrier', $user->getCarrier());
        $this->add('totalAmountBilled', $this->getDataFromReportingTool($user)['data']['charges_successful_value']);

        /** @var AffiliateLog $affiliateLog */
        $affiliateLog = $this->affLogRepository
            ->findOneBy(['userMsisdn' => $user->getIdentifier()], ['id' => 'DESC']);
        if (!empty($affiliateLog)) {
            $campaignParams = $affiliateLog->getCampaignParams();
            if (!empty($campaignParams) && !empty($campaignParams['cid'])) {
                $this->campaignData = $campaignParams;
            }
        }
    }

    private function getDataByCarrier(Carrier $carrier)
    {
        $this->add('carrier', $carrier);
        $this->add('country', $carrier->getCountryCode());
    }

    private function addCampaignData()
    {
        if (!empty($this->campaignData) && !empty($this->campaignData['cid'])) {
            $campaignEntity = $this->campaignRepository->findOneByCampaignToken($this->campaignData['cid']);

            $this->data['campaignURL'] = $this->router->generate(
                'landing',
                $this->campaignData,
                UrlGeneratorInterface::ABSOLUTE_URL
            );
            $this->data['campaignToken'] = $campaignEntity->getCampaignToken();
            $this->data['campaign'] = $campaignEntity;
            $this->data['campaignId'] = $campaignEntity->getUuid();
            $this->data['affiliateId'] = $campaignEntity->getAffiliate()->getUuid();
            $partner = $campaignEntity->getAffiliate()->getUuid();
            if ($partner instanceof Affiliate) {
                $this->data['notificationURL'] = $partner->getPostbackUrl();
            }
        }

    }

    private function setupDefaultData()
    {
        $this->data['user'] = null;
        $this->data['subscription'] = null;
        $this->data['subscriptionProducts'] = null;
        $this->data['gameDownloadCount'] = null;
        $this->data['campaign'] = '';
        $this->data['campaignURL'] = 'no campaign';
        $this->data['campaignToken'] = 'no campaign';
        $this->data['affiliateId'] = 'no campaign';
        $this->data['campaignId'] = 'no campaign';
        $this->data['deviceDetection'] = $this->deviceDetectionService;
        $this->data['connectionInfo'] = $this->maxMindIpInfoService;
        $this->data['notificationURL'] = 'no URL';
    }

    private function getDataFromReportingTool(User $User)
    {
        $url = 'http://reporting.100sport.tv/api/stats/userstats/' . $User->getBillingCarrierId();
        $key = sha1(date("Y") . $User->getIdentifier() . date("d"));

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url,
            CURLOPT_POSTFIELDS => ['msisdn' => $User->getIdentifier()]
        ]);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'X-REVERSE-KEY: ' . $key,
        ]);
        $response = json_decode(curl_exec($curl), true);
        curl_close($curl);

        return $response;
    }


    public function sendMessage($data): void
    {
        $content = $this->templating->render('@App/Mails/contact-us-notification.html.twig', $data);

        $message = $this->composer->compose($content);

        $this->mailer->send($message);

    }

}