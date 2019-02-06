<?php

namespace SubscriptionBundle\Admin\Form\Unsubscription;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class UnsubscribeByMsisdnForm
 */
class UnsubscribeByMsisdnForm extends AbstractType
{
    const NAME = 'msisdn';

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('msisdn', TextType::class, [
                'required' => true,
                'label' => 'Please enter the msisdn'
            ]);
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return self::NAME;
    }
}