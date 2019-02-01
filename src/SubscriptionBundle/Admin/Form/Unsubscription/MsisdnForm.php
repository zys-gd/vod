<?php

namespace SubscriptionBundle\Admin\Form\Unsubscription;

use Symfony\Component\Form\AbstractType;

/**
 * Class MsisdnForm
 */
class MsisdnForm extends AbstractType
{
    const NAME = 'msisdn';

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return self::NAME;
    }
}