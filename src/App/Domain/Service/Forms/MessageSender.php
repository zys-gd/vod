<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 05.02.19
 * Time: 11:56
 */

namespace App\Domain\Service\Forms;

use App\Domain\Service\Email\EmailComposer;
use Swift_Mailer;
use Twig_Environment;


class MessageSender
{
    /**
     * @var \App\Domain\Service\Email\EmailComposer $composer
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
     *
     * @param EmailComposer     $composer
     * @param \Twig_Environment $templating
     * @param \Swift_Mailer     $mailer
     */
    public function __construct(EmailComposer $composer,
        Twig_Environment $templating,
        Swift_Mailer $mailer
    )
    {

        $this->composer = $composer;
        $this->templating = $templating;
        $this->mailer = $mailer;
    }

    /**
     * @param $data
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function sendMessage($data, string $twig): void
    {
        $content = $this->templating->render($twig, $data);

        $message = $this->composer->compose($content);

        $this->mailer->send($message);

    }

}