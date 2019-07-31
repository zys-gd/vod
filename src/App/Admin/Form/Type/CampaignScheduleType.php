<?php


namespace App\Admin\Form\Type;


use App\Domain\Entity\CampaignSchedule;
use ExtrasBundle\Utils\UuidGenerator;
use Sonata\AdminBundle\Form\Type\ChoiceFieldMaskType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CampaignScheduleType extends AbstractType
{
    /**
     * date('N')
     */
    const DAYS = [
        'Monday'    => 1,
        'Tuesday'   => 2,
        'Wednesday' => 3,
        'Thursday'  => 4,
        'Friday'    => 5,
        'Saturday'  => 6,
        'Sunday'    => 7,
    ];

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $required = true;
        $builder
            ->add('dayStart', ChoiceFieldMaskType::class, [
                'required' => $required,
                'choices'  => self::DAYS,
                'attr'     => ['class' => 'short_field', 'data-sonata-select2' => 'false']
            ]);

        $builder
            ->add('timeStart', TimeType::class, [
                'required' => $required,
                'attr'     => ['class' => 'short_field']
            ]);

        $builder
            ->add('dayEnd', ChoiceFieldMaskType::class, [
                'required' => $required,
                'choices'  => self::DAYS,
                'attr'     => ['class' => 'short_field', 'data-sonata-select2' => 'false']
            ]);

        $builder
            ->add('timeEnd', TimeType::class, [
                'required' => $required,
                'attr'     => ['class' => 'short_field']
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CampaignSchedule::class,
            'empty_data' => function () {
                return new CampaignSchedule(UuidGenerator::generate());
            }
        ]);
    }
}