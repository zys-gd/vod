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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ContactUsController extends AbstractController
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
     * ContactUsController constructor.
     * @param FormFactoryInterface   $formFactory
     * @param ContactUsMessageSender $contactUsMessageSender
     */
    public function __construct(FormFactoryInterface $formFactory, ContactUsMessageSender $contactUsMessageSender)
    {
        $this->formFactory            = $formFactory;
        $this->contactUsMessageSender = $contactUsMessageSender;
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

        return $this->render(
            '@App/Content/contact_us.html.twig', [
                'form' => $form->createView()
            ]
        );
    }
}