<?php


namespace IdentificationBundle\Form;

use libphonenumber\PhoneNumberUtil;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Constraints\Callback;

class LPConfirmSMSPinCodeType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('pin_code', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                    new NotNull(),
                ],
                'invalid_message' => "Invalid 'pin_code' field"
            ]);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'pin_code' => null,
            'pin_validation_pattern' => null,
            'constraints' => [
                new Callback([$this, 'validatePinCode'])
            ]
        ]);
    }

    public function validatePinCode(array $data, ExecutionContextInterface $context): void
    {
        $pinCode = $data['pin_code'];
        $pinPattern = $data['pin_validation_pattern'];
        if($pinCode && $pinPattern && !preg_match("/$pinPattern/",$pinCode)) {
            $context
                ->buildViolation('Invalid `pin_code` format')
                ->addViolation();
        }
    }

}