<?php
namespace PriceBundle\Form\Type;

use PriceBundle\Entity\TierValue;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
* Build the tier value add form
*
* @package CompanyBundle\Form\Type
*/
class TierValueType extends AbstractAdmin
{

    /**
    * Build the tier value type
    *
    * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    *
    * @param FormBuilderInterface $builder
    * @param array $options
    */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('carrierId')
            ->add('billingAgregatorId')
            ->add('value')
            ->add('currency');
    }

    /**
    * @inheritdoc
    * @param OptionsResolver $resolver
    */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TierValue::class,
            'csrf_protection' => false,
            'allow_extra_fields' => true
        ]);
    }
}