<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 29.01.19
 * Time: 14:19
 */

namespace SubscriptionBundle\Twig;


use IdentificationBundle\Identification\Service\IdentificationFlowDataExtractor;
use IdentificationBundle\Repository\UserRepository;
use SubscriptionBundle\Repository\SubscriptionRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SubscriptionStatusExtension extends \Twig_Extension
{
    /**
     * @var SessionInterface
     */
    private $session;
    /**
     * @var UserRepository
     */
    private $repository;
    /**
     * @var SubscriptionRepository
     */
    private $subscriptionRepository;


    /**
     * SubscriptionStatusExtension constructor.
     * @param SessionInterface       $session
     * @param UserRepository         $repository
     * @param SubscriptionRepository $subscriptionRepository
     */
    public function __construct(SessionInterface $session, UserRepository $repository, SubscriptionRepository $subscriptionRepository)
    {
        $this->session                = $session;
        $this->repository             = $repository;
        $this->subscriptionRepository = $subscriptionRepository;
    }

    public function getFunctions()
    {
        return [new \Twig_SimpleFunction('isSubscribed', function () {
            $extractIdentificationData = IdentificationFlowDataExtractor::extractIdentificationData($this->session);
            if (isset($extractIdentificationData['identification_token'])) {
                $user = $this->repository->findOneByIdentificationToken($extractIdentificationData['identification_token']);
                if ($user) {
                    $subscription = $this->subscriptionRepository->findCurrentSubscriptionByOwner($user);

                    return (bool)$subscription;
                }
            }
            return false;


        })];
    }


}