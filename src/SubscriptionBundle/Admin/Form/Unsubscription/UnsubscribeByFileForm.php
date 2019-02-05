<?php

namespace SubscriptionBundle\Admin\Form\Unsubscription;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;

/**
 * Class UnsubscribeByFileForm
 */
class UnsubscribeByFileForm extends AbstractType
{
    const NAME = 'file';

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file', FileType::class, [
                'constraints' => [
                    new File([
                        'mimeTypes' => [
                            'text/csv',
                            'text/plain',
                            'text/tsv'
                        ],
                        'mimeTypesMessage' => 'Invalid file format. Available extensions .csv, .tsv, .txt (comma separated text files)'
                    ])
                ],
                'required' => true,
                'label' => 'Choose the .csv file'
            ]);
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return self::NAME;
    }
}