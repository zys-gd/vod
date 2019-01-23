<?php

namespace App\Admin\Form;

use App\Domain\Entity\Category;
use App\Domain\Repository\CategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class UploadVideoForm
 */
class UploadedVideoForm extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'max' => 255
                    ])
                ]
            ])
            ->add('mainCategory', EntityType::class, [
                'query_builder' => function (CategoryRepository $categoryRepository) {
                    $qb = $categoryRepository
                        ->createQueryBuilder('c')
                        ->where("c.parent IS NULL");

                    return $qb;
                },
                'choice_label' => 'title',
                'class' => Category::class,
                'mapped' => false,
                'placeholder' => 'Select main category',
                'required' => true
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'required' => true,
                'disabled' => true,
                'placeholder' => 'Select category'
            ])
            ->add('description', TextareaType::class, [
                'required' => false
            ])
            ->add('videoFile', FileType::class, [
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
                ]]
            );
    }
}