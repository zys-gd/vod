<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 19.09.19
 * Time: 15:09
 */

namespace SubscriptionBundle\API\Controller;


use ExtrasBundle\Controller\Traits\ResponseTrait;
use Psr\Log\LoggerInterface;
use SubscriptionBundle\API\Exception\NotFoundException;
use SubscriptionBundle\API\Service\LegacyBillingFormatter;
use SubscriptionBundle\API\Service\SubscriptionInfoProvider;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SubscriptionDataAction
{
    use ResponseTrait;
    /**
     * @var LegacyBillingFormatter
     */
    private $legacyBillingFormatter;
    /**
     * @var SubscriptionInfoProvider
     */
    private $provider;
    /**
     * @var LoggerInterface
     */
    private $logger;


    /**
     * SubscriptionDataAction constructor.
     * @param LegacyBillingFormatter   $legacyBillingFormatter
     * @param SubscriptionInfoProvider $provider
     */
    public function __construct(LegacyBillingFormatter $legacyBillingFormatter, SubscriptionInfoProvider $provider, LoggerInterface $logger)
    {
        $this->legacyBillingFormatter = $legacyBillingFormatter;
        $this->provider               = $provider;
        $this->logger                 = $logger;
    }

    public function __invoke(Request $request)
    {
        $identifier = $request->get('user', '');

        try {

            if (!$identifier) {
                throw new BadRequestHttpException('Missing `user` parameter');
            }

            $subscription = $this->provider->getSubscription($identifier);
            $formatted    = $this->legacyBillingFormatter->getFormattedData($subscription);

            return new JsonResponse($formatted);

        } catch (NotFoundException $exception) {
            return new JsonResponse(['error' => $exception->getMessage()], Response::HTTP_NOT_FOUND);

        } catch (\Exception $exception) {

            $this->logger->error($exception->getMessage(), ['trace' => $exception->getTrace()]);

            return new JsonResponse(['error' => 'Something has gone wrong'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }


    }


}