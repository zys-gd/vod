<?php

namespace App\Domain\Service\Email;

class EmailComposer
{


    const SUPPORT_FROM = 'noreply@vod.net';
    const SUPPORT_TO = 'support@vod.net';

    public function compose(string $content): \Swift_Mime_SimpleMessage
    {
        $message = new \Swift_Message('Contact us form notification');
        $message->setFrom(self::SUPPORT_FROM)
            ->setTo(self::SUPPORT_TO)
            ->setBody($content)
            ->setContentType('text/html');

        return $message;
    }

}

