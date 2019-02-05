<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 05.02.19
 * Time: 11:56
 */

namespace App\Domain\Service;


use App\Domain\Service\Email\EmailComposer;

class ContactUsMessageSender
{
    /**
     * @var App\Domain\Service\Email
     */
    private $composer;
    /**
     * @var \Twig_Environment
     */
    private $templating;
    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * ContactUsMessageSender constructor.
     * @param \App\Domain\Service\Email\EmailComposer $composer
     * @param \Twig_Environment                       $templating
     * @param \Swift_Mailer                           $mailer
     */
    public function __construct(EmailComposer $composer, \Twig_Environment $templating, \Swift_Mailer $mailer)
    {
        $this->composer   = $composer;
        $this->templating = $templating;
        $this->mailer     = $mailer;
    }

    public function sendMessage(string $email, string $comment): void
    {
        $content = $this->templating->render('@App/Mails/contact-us-notification.html.twig', [
            'email'   => $email,
            'comment' => $comment
        ]);

        $message = $this->composer->compose($content);

        $this->mailer->send($message);
    }
}