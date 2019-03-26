<?php

namespace App\Domain\Service\Email;

/**
 * Class EmailComposer
 */
class EmailComposer
{
    const SUPPORT_FROM = 'support.form@origin-data.com';
    const SUPPORT_TO   = 'denis.lukash@origin-data.com';

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * EmailComposer constructor
     *
     * @param \Twig_Environment $twig
     */
    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @param string $twigPath
     * @param array $data
     *
     * @return \Swift_Message
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function getContactUsMessage(string $twigPath, array $data): \Swift_Message
    {
        $body = $this->twig->render($twigPath, $data);

        $message = new \Swift_Message('Contact us form notification');
        $message->setFrom(self::SUPPORT_FROM)
            ->setTo(self::SUPPORT_TO)
            ->setBody($body)
            ->setContentType('text/html');

        return $message;
    }
}