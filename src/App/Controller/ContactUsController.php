<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 04.02.19
 * Time: 17:02
 */

namespace App\Controller;


use App\Domain\Entity\Campaign;
use App\Domain\Repository\CampaignRepository;
use App\Form\ContactUsType;
use ExtrasBundle\Email\EmailSender;
use SubscriptionBundle\Affiliate\Service\AffiliateVisitSaver;
use SubscriptionBundle\Service\SubscriptionExtractor;
use SubscriptionBundle\Service\UserExtractor;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ContactUsController extends AbstractController implements AppControllerInterface
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;
    /**
     * @var UserExtractor
     */
    private $userExtractor;
    /**
     * @var EmailSender
     */
    private $emailSender;
    /**
     * @var SubscriptionExtractor
     */
    private $subscriptionExtractor;
    /**
     * @var CampaignRepository
     */
    private $campaignRepository;
    /**
     * @var string
     */
    private $contactUsMailTo;
    /**
     * @var string
     */
    private $contactUsMailFrom;


    public function __construct(FormFactoryInterface $formFactory,
                                UserExtractor $userExtractor,
                                EmailSender $emailSender,
                                SubscriptionExtractor $subscriptionExtractor,
                                CampaignRepository $campaignRepository,
                                string $contactUsMailTo,
                                string $contactUsMailFrom
    ) {
        $this->formFactory = $formFactory;
        $this->userExtractor = $userExtractor;
        $this->emailSender = $emailSender;
        $this->subscriptionExtractor = $subscriptionExtractor;
        $this->campaignRepository = $campaignRepository;
        $this->contactUsMailTo = $contactUsMailTo;
        $this->contactUsMailFrom = $contactUsMailFrom;
    }


    /**
     * @Route("/contact-us",name="contact_us")
     */
    public function contactUsAction(Request $request)
    {
        $form = $this->formFactory->create(ContactUsType::class);

        $form->handleRequest($request);

        $user = $this->userExtractor->getUserFromRequest($request);
        $userIdentifier = is_null($user)
            ? null
            : $user->getIdentifier();

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $subscription = $user ? $this->subscriptionExtractor->getExistingSubscriptionForUser($user) : null;
            $campaignId = AffiliateVisitSaver::extractCampaignToken($request->getSession());
            /** @var Campaign|null $campaign */
            $campaign = $campaignId ? $this->campaignRepository->find($campaignId) : null;

            $data['requestHeaders'] = $request->headers->all();
            $data['user'] = $user;
            $data['subscription'] = $subscription;
            $data['campaign'] = $campaign;
            $data['affiliate'] = $campaign ? $campaign->getAffiliate() : null;

            $twig = '@App/Mails/contact-us-notification.html.twig';
            $subject = 'Contact us form notification';

            $this->emailSender->sendMessage($twig, $data, $subject, $this->contactUsMailFrom, $this->contactUsMailTo);

            return $this->render('@App/Mails/thank-you-mail.html.twig');
        }
        return $this->render(
            '@App/Content/contact_us.html.twig', [
                'form' => $form->createView(),
                'userIdentifier' => $userIdentifier
            ]
        );
    }
}