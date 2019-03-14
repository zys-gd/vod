<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 04.02.19
 * Time: 17:02
 */

namespace App\Controller;


use App\Domain\Service\Forms\MessageSender;
use App\Form\ContactUsType;
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


    public function __construct(FormFactoryInterface $formFactory,
        UserExtractor $userExtractor,
        MessageSender $messageSender)
    {
        $this->formFactory = $formFactory;
        $this->userExtractor = $userExtractor;
        $this->messageSender = $messageSender;
    }


    /**
     * @Route("/contact-us",name="contact_us")
     */
    public function contactUsAction(Request $request)
    {
        $form = $this->formFactory->create(ContactUsType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $twig = '@App/Mails/contact-us-notification.html.twig';
            $this->messageSender->sendMessage($data, $twig);

            return $this->render('@App/Mails/thank-you-mail.html.twig');
        }

        $user = $this->userExtractor->getUserFromRequest($request);
        $userIdentifier = is_null($user)
            ? null
            : $user->getIdentifier();
        return $this->render(
            '@App/Content/contact_us.html.twig', [
                'form' => $form->createView(),
                'userIdentifier' => $userIdentifier
            ]
        );
    }
}