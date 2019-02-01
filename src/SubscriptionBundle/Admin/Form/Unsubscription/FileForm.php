<?php

namespace SubscriptionBundle\Admin\Form\Unsubscription;

use Symfony\Component\Form\AbstractType;

/**
 * Class FileForm
 */
class FileForm extends AbstractType
{
    const NAME = 'file';

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return self::NAME;
    }
}