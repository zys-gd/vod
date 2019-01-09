<?php

namespace App\Admin\Form\Type;

use App\Domain\Entity\AffiliateConstant;
use App\Utils\UuidGenerator;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AffiliateConstantType
 */
class AffiliateConstantType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['required' => true])
            ->add('value', TextType::class, ['required' => true]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => AffiliateConstant::class,
            'empty_data' => function () {
                return new AffiliateConstant(UuidGenerator::generate());
            }
        ]);
    }
}