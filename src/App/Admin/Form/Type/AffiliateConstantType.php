<?php

namespace App\Admin\Form\Type;

use App\Domain\Entity\AffiliateConstant;
use ExtrasBundle\Utils\UuidGenerator;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

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
        $fieldOptions = [
            'required' => true,
            'constraints' => [
                new NotBlank(),
                new Length([
                    'max' => 255
                ])
            ]
        ];

        $builder
            ->add('name', TextType::class, $fieldOptions)
            ->add('value', TextType::class, $fieldOptions);
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