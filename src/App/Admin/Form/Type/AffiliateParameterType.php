<?php

namespace App\Admin\Form\Type;

use App\Domain\Entity\AffiliateParameter;
use App\Utils\UuidGenerator;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AffiliateParameterType
 */
class AffiliateParameterType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('inputName', TextType::class, ['required' => true])
            ->add('outputName', TextType::class, ['required' => true]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => AffiliateParameter::class,
            'empty_data' => function () {
                return new AffiliateParameter(UuidGenerator::generate());
            }
        ]);
    }
}