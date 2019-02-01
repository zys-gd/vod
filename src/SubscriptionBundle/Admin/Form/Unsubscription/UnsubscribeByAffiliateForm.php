<?php

namespace SubscriptionBundle\Admin\Form\Unsubscription;

use Symfony\Component\Form\AbstractType;

/**
 * Class UnsubscribeByAffiliateForm
 */
class UnsubscribeByAffiliateForm extends AbstractType
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