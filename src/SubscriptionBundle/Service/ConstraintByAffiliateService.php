<?php

namespace SubscriptionBundle\Service;

use ExtrasBundle\Cache\ICacheService;
use IdentificationBundle\Entity\CarrierInterface;
use IdentificationBundle\Repository\CarrierRepositoryInterface;
use SubscriptionBundle\Entity\Affiliate\AffiliateInterface;
use SubscriptionBundle\Entity\Affiliate\CampaignInterface;
use SubscriptionBundle\Entity\Affiliate\ConstraintByAffiliate;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Repository\Affiliate\CampaignRepositoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Class ConstraintByAffiliateService
 */
class ConstraintByAffiliateService
{
    /**
     * @var ICacheService
     */
    private $cache;

    /**
     * @var CampaignRepositoryInterface
     */
    private $campaignRepository;

    /**
     * @var CarrierInterface
     */
    private $carrier;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * ConstraintByAffiliateService constructor
     *
     * @param ICacheService $cacheService
     * @param CampaignRepositoryInterface $campaignRepository
     * @param CarrierRepositoryInterface $carrierRepository
     * @param SessionInterface $session
     * @param ContainerInterface $container
     * @param \Twig_Environment $twig
     */
    public function __construct(
        ICacheService $cacheService,
        CampaignRepositoryInterface $campaignRepository,
        CarrierRepositoryInterface $carrierRepository,
        SessionInterface $session,
        ContainerInterface $container,
        \Twig_Environment $twig
    ) {
        $this->cache = $cacheService;
        $this->campaignRepository = $campaignRepository;
        $this->container = $container;
        $this->twig = $twig;

        $ispDetectionData = $session->get('isp_detection_data');

        if (!empty($ispDetectionData['carrier_id'])) {
            $this->carrier = $this->carrier = $carrierRepository->findOneByBillingId($ispDetectionData['carrier_id']);
        }
    }

    /**
     * @param CampaignInterface $campaign
     *
     * @return RedirectResponse|null
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function handleLandingPageRequest(CampaignInterface $campaign)
    {
        $affiliate = $campaign->getAffiliate();
        $constraints = $affiliate->getConstraints();

        /** @var ConstraintByAffiliate $constraint */
        foreach ($constraints as $constraint) {
            $cacheKey = $this->getCacheKey($constraint);

            if (!$this->cache->hasCache($cacheKey)
                || ($this->carrier && $this->carrier->getUuid() !== $constraint->getCarrier()->getUuid())
            ) {
                continue;
            }

            $isLimitReached = $this->cache->getValue($cacheKey) >= $constraint->getNumberOfActions();

            if ($isLimitReached) {
                $this->sendCapNotification($constraint);

                return new RedirectResponse($constraint->getRedirectUrl());
            } elseif ($constraint->getCapType() === ConstraintByAffiliate::CAP_TYPE_VISIT) {
                $this->updateVisitCounter($constraint->getAffiliate());
            }
        }

        return null;
    }

    /**
     * @param Subscription $subscription
     */
    public function updateSubscribeCounter(Subscription $subscription): void
    {
        $affiliateToken = $subscription->getAffiliateToken();

        if (empty($affiliateToken['cid'])) {
            return;
        }

        /** @var CampaignInterface $campaign */
        $campaign = $this->campaignRepository->findOneByCampaignToken($affiliateToken['cid']);
        $affiliate = $campaign->getAffiliate();

        $campaignCarrierIds = array_map(function (CarrierInterface $carrier) {
            return $carrier->getUuid();
        }, $campaign->getCarriers()->getValues());

        if ($this->carrier && in_array($this->carrier->getUuid(), $campaignCarrierIds)) {
            $this->updateCounter($affiliate->getConstraint(ConstraintByAffiliate::CAP_TYPE_SUBSCRIBE));
        }
    }

    /**
     * @param AffiliateInterface $affiliate
     */
    private function updateVisitCounter(AffiliateInterface $affiliate): void
    {
        $this->updateCounter($affiliate->getConstraint(ConstraintByAffiliate::CAP_TYPE_VISIT));
    }

    /**
     * @param ConstraintByAffiliate|null $constraintByAffiliate
     */
    private function updateCounter(?ConstraintByAffiliate $constraintByAffiliate): void
    {
        if (!$constraintByAffiliate) {
            return;
        }

        $cacheKey = $this->getCacheKey($constraintByAffiliate);
        $counter = $this->cache->hasCache($cacheKey) ? $this->cache->getValue($cacheKey) + 1 : 1;

        $this->cache->saveCache($cacheKey, $counter, 86400);
    }

    /**
     * @param ConstraintByAffiliate $constraintByAffiliate
     *
     * @return string
     */
    private function getCacheKey(ConstraintByAffiliate $constraintByAffiliate): string
    {
        return 'counter_' . $constraintByAffiliate->getUuid();
    }

    /**
     * @param ConstraintByAffiliate $constraintByAffiliate
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    private function sendCapNotification(ConstraintByAffiliate $constraintByAffiliate)
    {
        if ($constraintByAffiliate->getIsCapAlertDispatch()) {
            return;
        }

        $transport = new \Swift_SmtpTransport($this->container->getParameter('mailer_host'));
        $transport
            ->setUsername($this->container->getParameter('mailer_user'))
            ->setPassword($this->container->getParameter('mailer_password'));

        $mailer = new \Swift_Mailer($transport);

        $mailFrom = 'pwintegrations@playwing.net';
        $mailTo = 'denis.lukash@origin-data.com';

        $capType = $constraintByAffiliate->getCapType();

        $subject = '[Alert] ' . ucfirst($capType) . ' CAP by affiliate reached';
        $body = $this->twig->render('@Subscription/ConstraintByAffiliate/Mail/cap_alert_template.html.twig', [
            'affiliateName' => $constraintByAffiliate->getAffiliate()->getName(),
            'carrierName' => $this->carrier ? $this->carrier->getName() : '',
            'actionsLimit' => $constraintByAffiliate->getNumberOfActions(),
            'capType' => $constraintByAffiliate->getCapType()
        ]);

        $message = new \Swift_Message($subject, $body, 'text/html');
        $message
            ->setFrom($mailFrom)
            ->setTo($mailTo);

        $isAlertSend = $mailer->send($message);
    }
}