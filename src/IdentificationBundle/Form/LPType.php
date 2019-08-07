<?php


namespace IdentificationBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class LPType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('mobile_number', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                    new NotNull(),
                    new Length(['min' => 6])
                ],
                'invalid_message' => "Invalid 'mobile_number' field"
            ]);
    }
}