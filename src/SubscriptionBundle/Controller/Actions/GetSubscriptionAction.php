<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 18/06/26
 * Time: 17:24
 */

namespace SubscriptionBundle\Controller\Actions;


use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use SubscriptionBundle\Controller\Traits\ResponseTrait;
use SubscriptionBundle\Service\BillableUserProvider;
use SubscriptionBundle\Service\SubscriptionProvider;

class GetSubscriptionAction
{

    use ResponseTrait;
    /**
     * @var BillableUserProvider
     */
    private $billableUserProvider;


    /**
     * @var SubscriptionProvider
     */
    private $provider;


    /**
     * GetSubscriptionAction constructor.
     */
    public function __construct(BillableUserProvider $billableUserProvider, SubscriptionProvider $provider)
    {
        $this->billableUserProvider = $billableUserProvider;
        $this->provider = $provider;
    }

    public function __invoke(Request $request)
    {

        try {
            /** @var \UserBundle\Entity\BillableUser $billableUser */
            $billableUser = $this->billableUserProvider->getFromRequest($request);
        } catch (\Exception $ex) {
            return $this->getSimpleJsonResponse($ex->getMessage(), 400, [
                'identification' => false,
                'subscription' => false,
            ]);
        }

        try {
            $subscription = $this->provider->getExistingSubscriptionForBillableUser($billableUser);
            if (!$subscription) {
                return $this->getSimpleJsonResponse(
                    sprintf('Can not find subscription for msisdn %s', $billableUser->getIdentifier()),
                    400,
                    [
                        'user' => true,
                        'subscription' => false,
                    ]
                );
            }
        } catch (\Exception $ex) {
            return $this->getSimpleJsonResponse($ex->getMessage(), 400, [
                'user' => true,
                'subscription' => false,
            ]);
        }

        return new JsonResponse(['id' => $subscription->getUuid()]);
    }

}