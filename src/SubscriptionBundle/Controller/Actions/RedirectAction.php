<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 30.04.18
 * Time: 12:21
 */

namespace SubscriptionBundle\Controller\Actions;


use IdentificationBundle\Entity\User;
use IdentificationBundle\Exception\RedirectRequiredException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use SubscriptionBundle\Service\UserExtractor;
use SubscriptionBundle\Service\SubscriptionProvider;

class RedirectAction extends Controller
{
    /**
     * @var UserExtractor
     */
    private $userExtractor;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var SubscriptionProvider
     */
    private $subscriptionProvider;

    /**
     * RedirectAction constructor.
     *
     * @param UserExtractor        $userExtractor
     * @param LoggerInterface      $logger
     * @param SubscriptionProvider $subscriptionProvider
     */
    public function __construct(
        UserExtractor $userExtractor,
        LoggerInterface $logger,
        SubscriptionProvider $subscriptionProvider
    )
    {
        $this->userExtractor = $userExtractor;
        $this->logger               = $logger;
        $this->subscriptionProvider = $subscriptionProvider;
    }


    /**
     * This function is added to handle the need for redirect after getting redirect response from billing framework.
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function __invoke(Request $request)
    {
        $redirectURL = $request->getSchemeAndHttpHost();

        try {
            /** @var User $user */
            $user = $this->userExtractor->getUserFromRequest($request);
            $subscription = $this->subscriptionProvider->getExistingSubscriptionForUser($user);

            if (isset($subscription) && $subscription->isRedirectRequired()) {
                $redirectURL = $subscription->getRedirectUrl();
            } else {
                throw new RedirectRequiredException($redirectURL);
            }

        } catch (RedirectRequiredException $ex) {
            $redirectURL = $ex->getRedirectUrl();
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
        }
        return $this->redirect($redirectURL);
    }
}