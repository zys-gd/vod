<?php

namespace ExtrasBundle\Email;

/**
 * Class EmailComposer
 */
class EmailComposer
{
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
     * @param string $subject
     * @param string $from
     * @param string $to
     *
     * @return \Swift_Message
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function compose(string $twigPath, array $data, string $subject, string $from, string $to): \Swift_Message
    {
        $body = $this->twig->render($twigPath, $data);

        $message = new \Swift_Message($subject);
        $message
            ->setFrom($from)
            ->setTo($to)
            ->setBody($body)
            ->setContentType('text/html');

        return $message;
    }
}