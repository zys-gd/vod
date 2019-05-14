<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 04.02.19
 * Time: 17:02
 */

namespace App\Controller;


use App\CarrierTemplate\TemplateConfigurator;
use App\Domain\Entity\Campaign;
use App\Domain\Repository\CampaignRepository;
use App\Form\ContactUsType;
use CountryCarrierDetectionBundle\Service\MaxMindIpInfo;
use DeviceDetectionBundle\Service\Device;
use ExtrasBundle\Email\EmailSender;
use IdentificationBundle\Identification\DTO\ISPData;
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
    /**
     * @var TemplateConfigurator
     */
    private $templateConfigurator;
    /**
     * @var Device
     */
    private $device;
    /**
     * @var MaxMindIpInfo
     */
    private $maxMindIpInfo;

    /**
     * ContactUsController constructor.
     *
     * @param FormFactoryInterface  $formFactory
     * @param UserExtractor         $userExtractor
     * @param EmailSender           $emailSender
     * @param SubscriptionExtractor $subscriptionExtractor
     * @param CampaignRepository    $campaignRepository
     * @param string                $contactUsMailTo
     * @param string                $contactUsMailFrom
     * @param TemplateConfigurator  $templateConfigurator
     * @param Device                $device
     * @param MaxMindIpInfo         $maxMindIpInfo
     */
    public function __construct(FormFactoryInterface $formFactory,
        UserExtractor $userExtractor,
        EmailSender $emailSender,
        SubscriptionExtractor $subscriptionExtractor,
        CampaignRepository $campaignRepository,
        string $contactUsMailTo,
        string $contactUsMailFrom,
        TemplateConfigurator $templateConfigurator,
        Device $device,
        MaxMindIpInfo $maxMindIpInfo
    )
    {
        $this->formFactory           = $formFactory;
        $this->userExtractor         = $userExtractor;
        $this->emailSender           = $emailSender;
        $this->subscriptionExtractor = $subscriptionExtractor;
        $this->campaignRepository    = $campaignRepository;
        $this->contactUsMailTo       = $contactUsMailTo;
        $this->contactUsMailFrom     = $contactUsMailFrom;
        $this->templateConfigurator  = $templateConfigurator;
        $this->device                = $device;
        $this->maxMindIpInfo         = $maxMindIpInfo;
    }


    /**
     * @Route("/contact-us",name="contact_us")
     * @param Request $request
     * @param ISPData $data
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function contactUsAction(Request $request, ISPData $data)
    {
        $form = $this->formFactory->create(ContactUsType::class);

        $form->handleRequest($request);

        $user           = $this->userExtractor->getUserFromRequest($request);
        $userIdentifier = is_null($user)
            ? null
            : $user->getIdentifier();

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $subscription = $user ? $this->subscriptionExtractor->getExistingSubscriptionForUser($user) : null;
            $campaignId   = AffiliateVisitSaver::extractCampaignToken($request->getSession());
            /** @var Campaign|null $campaign */
            $campaign = $campaignId ? $this->campaignRepository->findOneBy(['campaignToken' => $campaignId]) : null;

            $data['requestHeaders'] = $request->headers->all();
            $data['user']           = $user;
            $data['subscription']   = $subscription;
            $data['campaign']       = $campaign;
            $data['affiliate']      = $campaign ? $campaign->getAffiliate() : null;
            $data['device']         = $this->device;
            $data['maxMindIpInfo']  = $this->maxMindIpInfo;


            $twig    = '@App/Mails/contact-us-notification.html.twig';
            $subject = 'Contact us form notification';

            $this->emailSender->sendMessage($twig, $data, $subject, $this->contactUsMailFrom, $this->contactUsMailTo);

            return $this->render('@App/Mails/thank-you-mail.html.twig');
        }
        $template = $this->templateConfigurator->getTemplate('contact_us', $data->getCarrierId());
        return $this->render($template, [
                'form'           => $form->createView(),
                'userIdentifier' => $userIdentifier
            ]
        );
    }
}