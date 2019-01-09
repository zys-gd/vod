<?php

namespace SubscriptionBundle\Service\SubscriptionText;


class DeclensionHelper
{
    /**
     * @param int $credits
     * @return string
     */
    public function getCreditsDeclension(int $credits): string
    {
        return $credits == 1 ? 'game' : 'games';
    }
}