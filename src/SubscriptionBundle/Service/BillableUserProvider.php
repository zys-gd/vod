<?php
/**
 * Created by IntelliJ IDEA.
 * User: bharatm
 * Date: 03/08/17
 * Time: 12:51 PM
 */

namespace SubscriptionBundle\Service;


use IdentificationBundle\Exception\RedirectRequiredException;
use IdentificationBundle\Identity\IdentityInterface;
use IdentificationBundle\Service\IdentificationService;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use UserBundle\Entity\BillableUser;
use UserBundle\Service\UsersService;

class BillableUserProvider
{


    /** @var  IdentificationService */
    private $identificationService;

    /** @var  UsersService */
    private $usersService;

    /** @var  string */
    private $tokenSessionName;

    /** @var  RouterInterface */
    private $router;
    /**
     * @var SessionInterface
     */
    private $session;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * BillableUserHelperService constructor.
     * @param SessionInterface      $session
     * @param IdentificationService $identificationService
     * @param UsersService          $usersService
     * @param RouterInterface       $router
     * @param string                $tokenSessionName
     * @param LoggerInterface       $logger
     */
    public function __construct(
        SessionInterface $session,
        IdentificationService $identificationService,
        UsersService $usersService,
        RouterInterface $router,
        string $tokenSessionName,
        LoggerInterface $logger
    )
    {
        $this->identificationService = $identificationService;
        $this->usersService          = $usersService;
        $this->router                = $router;
        $this->tokenSessionName      = $tokenSessionName;
        $this->session               = $session;
        $this->logger                = $logger;
    }

    /**
     * @param Request $request
     * @return BillableUser
     * @throws RedirectRequiredException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \IdentificationBundle\Exception\UndefinedIdentityException
     */
    public function getFromRequest(Request $request): BillableUser
    {

        $tokenId = $request->getSession()->get($this->tokenSessionName);
        $this->logger->debug('Retrieving billable user from request', [
            'tokenId'          => $tokenId,
            'tokenSessionName' => $this->tokenSessionName,
            'session'          => $request->getSession()->all()
        ]);

        if (!$tokenId) {


            $wrongOperatorUrl = $this->router->generate(
                'wrong_operator',
                [],
                UrlGeneratorInterface::ABSOLUTE_PATH
            );
            $this->logger->debug('No token id found. Redirecting via exception', ['redirectUrl' => $wrongOperatorUrl]);
            throw new RedirectRequiredException($wrongOperatorUrl);
        }

        $identificationResponse = $this->identificationService->getIdentifyResponseByParameter($request, $tokenId);

        $this->logger->debug('Obtained identification Response by token id', ['tokenId' => $tokenId]);

        /** @var IdentityInterface $userIdentity */
        $userIdentity = $this->identificationService->getUserIdentityByTransactionToken(
            $identificationResponse->getTransactionToken()
        );

        $this->logger->debug('Obtained identity by transaction token token id', [
            'token'    => $identificationResponse->getTransactionToken(),
            'identity' => $userIdentity->getIdentifier()
        ]);


        /** @var BillableUser $billableUser */
        $billableUser = $this->usersService->getBillableUserByIdentity($userIdentity);


        $this->logger->debug('Obtained billable user', [
            'billableUserId' => $billableUser->getId(),
            'msidsn'         => $billableUser->getIdentifier()
        ]);


        return $billableUser;
    }


}