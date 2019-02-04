<?php

namespace SubscriptionBundle\Admin\Form\Unsubscription;

use App\Domain\Entity\Affiliate;
use App\Domain\Entity\Carrier;
use Sonata\Form\Type\DateTimeRangePickerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Range;

/**
 * Class UnsubscribeByAffiliateForm
 */
class UnsubscribeByAffiliateForm extends AbstractType
{
    const NAME = 'affiliate';

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('affiliate', EntityType::class, [
                'class' => Affiliate::class,
                'required' => true,
                'label' => 'Select affiliate to get all subscribed users',
                'placeholder' => 'Select affiliate',
            ])
            ->add('carrier', EntityType::class, [
                'class' => Carrier::class,
                'required' => true,
                'label' => 'and also please select carrier',
                'placeholder' => 'Select carrier',
            ])
            ->add('period', DateTimeRangePickerType::class, [
                'label' => 'and period of time when users has been subscribed',
                'required' => true,
                'field_options_start' => ['format' => 'yyyy-MM-dd'],
                'field_options_end' => ['format' => 'yyyy-MM-dd']
            ])
            ->add('usersCount', IntegerType::class, [
                'label' => 'also you have to select amount of users to unsubscribe',
                'constraints' => [
                    new Range([
                        'min' => 0,
                        'max' => 200
                    ])
                ],
                'attr' => ['min' => 0, 'max' => 200, 'style' => 'margin-left:10px;'],
                'required' => true,
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