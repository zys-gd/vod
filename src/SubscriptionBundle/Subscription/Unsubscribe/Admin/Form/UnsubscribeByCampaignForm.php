<?php

namespace SubscriptionBundle\Subscription\Unsubscribe\Admin\Form;

use App\Domain\Entity\Campaign;
use Sonata\Form\Type\DateTimeRangePickerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Range;

/**
 * Class UnsubscribeByCampaignForm
 */
class UnsubscribeByCampaignForm extends AbstractType
{
    const NAME = 'campaign';

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('campaign', EntityType::class, [
                'class'    => Campaign::class,
                'multiple' => true,
                'required' => true,
                'label'    => 'Choose campaigns to get all of users subscribed from them'
            ])
            ->add('period', DateTimeRangePickerType::class, [
                'label'               => 'and period of time when users has been subscribed',
                'required'            => true,
                'field_options_start' => ['format' => 'yyyy-MM-dd HH:mm:ss'],
                'field_options_end'   => ['format' => 'yyyy-MM-dd HH:mm:ss']
            ])
            ->add('usersCount', IntegerType::class, [
                'label'       => 'also you have to select amount of users to unsubscribe',
                'constraints' => [
                    new Range([
                        'min' => 0,
                        'max' => 200
                    ])
                ],
                'attr'        => ['min' => 0, 'max' => 200, 'style' => 'margin-left:10px;'],
                'required'    => true,
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