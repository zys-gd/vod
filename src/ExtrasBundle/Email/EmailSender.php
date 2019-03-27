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
     * @var EmailComposer
     */
    private $emailComposer;

    /**
     * ContactUsMessageSender constructor.
     *
     * @param \Swift_Mailer $mailer
     * @param EmailComposer $emailComposer
     */
    public function __construct(Swift_Mailer $mailer, EmailComposer $emailComposer)
    {
        $this->mailer = $mailer;
        $this->emailComposer = $emailComposer;
    }

    /**
     * @param string $twigPath
     * @param array $data
     * @param string $subject
     * @param string $from
     * @param string $to
     *
     * @return int
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function sendMessage(string $twigPath, array $data, string $subject, string $from, string $to): int
    {
        $message = $this->emailComposer->compose($twigPath, $data, $subject, $from, $to);

        return $this->mailer->send($message);
    }
}