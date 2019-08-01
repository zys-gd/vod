<?php

namespace SubscriptionBundle\Subscription\Unsubscribe\Admin\Form;

use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use Doctrine\ORM\EntityManagerInterface;
use Sonata\Form\Type\DateTimeRangePickerType;
use SubscriptionBundle\Entity\Affiliate\AffiliateInterface;
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
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * UnsubscribeByAffiliateForm constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('affiliate', EntityType::class, [
                'class'       => RealClassnameResolver::resolveName(AffiliateInterface::class, $this->entityManager),
                'required'    => true,
                'label'       => 'Select affiliate to get all subscribed users',
                'placeholder' => 'Select affiliate',
            ])
            ->add('carrier', EntityType::class, [
                'class'       => RealClassnameResolver::resolveName(CarrierInterface::class, $this->entityManager),
                'required'    => true,
                'label'       => 'and also please select carrier',
                'placeholder' => 'Select carrier',
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