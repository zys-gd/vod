<?php

namespace ExtrasBundle\Email;

use Swift_Mailer;

/**
 * Class EmailSender
 */
class EmailSender
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * ContactUsMessageSender constructor.
     *
     * @param \Swift_Mailer $mailer
     */
    public function __construct(Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @param \Swift_Message $message
     */
    public function sendMessage(\Swift_Message $message): void
    {
        $this->mailer->send($message);
    }
}