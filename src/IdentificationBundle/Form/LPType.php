<?php


namespace IdentificationBundle\Form;


use IdentificationBundle\Identification\Service\Session\IdentificationFlowDataExtractor;
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
                    new Length(['min' => 5]),
                ],
                'invalid_message' => "Invalid 'mobile_number' field"
            ]);

    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'country' => null,
            'mobile_number' => null,
            'pin_code' => null,
            'pin_validation_pattern' => null,
            'constraints' => [
                new Callback([$this, 'validatePhoneNumberByCountryCode']),
                new Callback([$this, 'validatePinCode'])
            ]
        ]);
    }

    public function validatePhoneNumberByCountryCode(array $data, ExecutionContextInterface $context): void
    {
        $countryCode = $data['country'];
        $phoneNumber = $data['mobile_number'];

        $phoneNumberUtil = PhoneNumberUtil::getInstance();
        $phoneNumber = $phoneNumberUtil->parse($phoneNumber);
        $foundCountryCode = $phoneNumberUtil->getRegionCodeForNumber($phoneNumber);

        if ($countryCode != $foundCountryCode) {
            $context
                ->buildViolation('Invalid `mobile number` format')
                ->addViolation();
        }
    }

    public function validatePinCode(array $data, ExecutionContextInterface $context): void
    {
        $pinCode = $data['pin_code'] ?? '';
        $pinPattern = $data['pin_validation_pattern'] ?? '';
        if($pinCode && $pinPattern && !preg_match("/$pinPattern/",$pinCode)) {
            $context
                ->buildViolation('Invalid `pin_code` format')
                ->addViolation();
        }
    }
}