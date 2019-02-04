<?php

namespace SubscriptionBundle\Admin\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;

/**
 * Class RefundForm
 */
class RefundForm extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('identifier', TextType::class, [
                'required' => false,
                'attr' => ['class' => 'col-md-4 mb-3 form-control'],
                'label' => 'Please enter the msisdn'
            ])
            ->add('file', FileType::class, [
                'required' => false,
                'attr' => ['class' => 'col-md-4 mb-3 form-control'],
                'constraints' => [
                    new File([
                        'mimeTypes' => [
                            'text/csv',
                            'text/plain',
                            'text/tsv',
                            'application/vnd.ms-excel'
                        ],
                        'mimeTypesMessage' => 'Invalid file format. Available extensions .csv, .tsv (comma separated text files)'
                    ])
                ],
                'label' => 'Or choose the .csv file'
            ]);
    }
}