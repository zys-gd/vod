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

class SendSMSPinCodeType extends AbstractType
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
            'constraints' => [
                new Callback([$this, 'validatePhoneNumberByCountryCode']),
            ],
            'csrf_protection' => false,
            'allow_extra_fields'=> true
        ]);
    }

    /**
     * @param array                     $data
     * @param ExecutionContextInterface $context
     * @throws \libphonenumber\NumberParseException
     */
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
}