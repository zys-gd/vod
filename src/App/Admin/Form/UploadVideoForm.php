<?php

namespace App\Admin\Form;

use App\Domain\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class UploadVideoForm
 */
class UploadVideoForm extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'title',
                TextType::class,
                [
                    'constraints' => [
                        new NotBlank(),
                        new Length([
                            'max' => 255
                        ])
                    ]
                ]
            )
            ->add(
                'category',
                EntityType::class,
                [
                    'class' => Category::class,
                    'choice_label' => 'title'
                ]
            )
            ->add(
                'file',
                FileType::class,
                [
                    'label' => 'File',
                    'constraints' => [
                        new File([
                            'maxSize' => '500M',
                            'mimeTypes' => [
                                'video/mp4'
                            ],
                            'mimeTypesMessage' => 'Please upload a valid MP4 file',
                            'uploadFormSizeErrorMessage' => 'Maximum file size exceeded, allowed size - 500M'
                        ])
                    ]
                ]
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {

    }
}
