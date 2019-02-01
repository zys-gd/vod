<?php

namespace SubscriptionBundle\Admin\Form\Unsubscription;

use Symfony\Component\Form\AbstractType;

/**
 * Class AffiliateForm
 */
class AffiliateForm extends AbstractType
{
    const NAME = 'affiliate';

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return self::NAME;
    }
}