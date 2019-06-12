<?php


namespace App\Admin\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class CampaignCloneForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('count', IntegerType::class, [
                    'attr' => [
                        'min'   => 1,
                        'value' => 1
                    ]
                ]
            )
            ->add('Clone', SubmitType::class, [
                'attr' => ['class' => 'btn btn-success']
            ]);
    }
}