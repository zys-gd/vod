<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 04.02.19
 * Time: 17:02
 */

namespace App\Controller;


use App\Domain\Service\ContactUsMessageSender;
use App\Form\ContactUsType;
use IdentificationBundle\Repository\UserRepository;
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
     * @var ContactUsMessageSender
     */
    private $contactUsMessageSender;
    /**
     * @var UserExtractor
     */
    private $userExtractor;

    /**
     * ContactUsController constructor.
     *
     * @param FormFactoryInterface $formFactory
     * @param ContactUsMessageSender $contactUsMessageSender
     * @param UserExtractor $userExtractor
     */
    public function __construct(FormFactoryInterface $formFactory,
        ContactUsMessageSender $contactUsMessageSender,
        UserExtractor $userExtractor)
    {
        $this->formFactory = $formFactory;
        $this->contactUsMessageSender = $contactUsMessageSender;
        $this->userExtractor = $userExtractor;
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

            $this->contactUsMessageSender->sendMessage($data['email'], $data['comment']);
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