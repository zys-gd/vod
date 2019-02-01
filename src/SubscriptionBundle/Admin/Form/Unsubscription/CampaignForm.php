<?php

namespace SubscriptionBundle\Admin\Form\Unsubscription;

use Symfony\Component\Form\AbstractType;

class CampaignForm extends AbstractType
{
    const NAME = 'campaign';

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return self::NAME;
    }
}