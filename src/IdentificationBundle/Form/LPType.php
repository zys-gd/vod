<?php


namespace IdentificationBundle\Form;


use App\Domain\Entity\Country;
use SubscriptionBundle\Service\LPDataExtractor;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
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
        /** @var LPDataExtractor $dataExtractor */
        $dataExtractor = $options['data']['lpDataExtractor'];

        $builder
            ->add('country', ChoiceType::class, [
            'constraints' => [
                new NotBlank(),
                new NotNull()
            ],
            'choices' => $dataExtractor->getActiveCarrierCountries()->map(function (Country $country) {
                return $country->getCountryCode();
            })->toArray(),
            'invalid_message' => "Invalid 'country' field"
        ])
            ->add('carrier_id', IntegerType::class, [
                'constraints' => [
                    new NotBlank(),
                    new NotNull()
                ],
                'invalid_message' => "Invalid 'carrier_id' field"
            ])
            ->add('mobile_number', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                    new NotNull(),
                    new Length(['min' => 6])
                ],
                'invalid_message' => "Invalid 'mobile_number' field"
            ]);
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'lpDataExtractor' => null
        ));
    }
}