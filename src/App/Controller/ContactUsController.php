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
use App\Domain\Service\Forms\MessageSender;
use App\Form\ContactUsType;
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
     * @var MessageSender
     */
    private $messageSender;
    /**
     * @var SubscriptionExtractor
     */
    private $subscriptionExtractor;
    /**
     * @var CampaignRepository
     */
    private $campaignRepository;


    public function __construct(FormFactoryInterface $formFactory,
        UserExtractor $userExtractor,
        MessageSender $messageSender,
        SubscriptionExtractor $subscriptionExtractor,
        CampaignRepository $campaignRepository)
    {
        $this->formFactory = $formFactory;
        $this->userExtractor = $userExtractor;
        $this->messageSender = $messageSender;
        $this->subscriptionExtractor = $subscriptionExtractor;
        $this->campaignRepository = $campaignRepository;
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
            $this->messageSender->sendMessage($data, $twig);

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