<?php

namespace App\Admin\Form;

use App\Domain\Entity\MainCategory;
use App\Domain\Entity\Subcategory;
use App\Domain\Entity\UploadedVideo;
use App\Domain\Repository\SubcategoryRepository;
use App\Utils\UuidGenerator;
use Sonata\Form\Type\DateTimePickerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\GreaterThan;
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
            ->add('description', TextareaType::class, [
                'required' => false
            ])
            ->add('expiredDate', DateTimePickerType::class, [
                'required' => false,
                'format' => 'Y-MM-dd HH:mm',
                'attr' => ['autocomplete' => 'off']
            ])
            ->add('isTrim', CheckboxType::class, [
                'label' => 'Trim video',
                'mapped' => false,
                'required' => false
            ])
            ->add('startOffset', IntegerType::class, [
                'constraints' => [
                    new GreaterThan(1)
                ],
                'mapped' => false,
                'label' => 'Seconds to trim from start',
                'required' => false
            ])
            ->add('file', FileType::class, [
                'label' => 'File',
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please select file'
                    ]),
                    new File([
                        'maxSize' => '500M',
                        'mimeTypes' => [
                            'video/mp4'
                        ],
                        'mimeTypesMessage' => 'Please upload a valid MP4 file',
                        'uploadFormSizeErrorMessage' => 'Maximum file size exceeded, allowed size - 500M'
                    ])
                ]
            ]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $uploadedVideo = $event->getData();
            $this->addCategoryFields($event->getForm(), $uploadedVideo ? $uploadedVideo['mainCategory'] : null);
        });

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();
            $this->addCategoryFields($event->getForm(), $data['mainCategory']);
        });
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UploadedVideo::class,
            'empty_data' => function (FormInterface $form) {
                return new UploadedVideo(UuidGenerator::generate());
            }
        ]);
    }

    /**
     * @param FormInterface $form
     * @param string $mainCategoryId
     */
    private function addCategoryFields(FormInterface $form, string $mainCategoryId = null)
    {
        $form
            ->add('mainCategory', EntityType::class, [
                'class' => MainCategory::class,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please select main category'
                    ])
                ],
                'mapped' => false,
                'placeholder' => 'Select main category'
            ])
            ->add('subcategory', EntityType::class, [
                'query_builder' => function (SubcategoryRepository $subcategoryRepository) use ($mainCategoryId) {
                    return $subcategoryRepository
                        ->createQueryBuilder('sc')
                        ->where('sc.parent = :mainId')
                        ->setParameter('mainId', $mainCategoryId);
                },
                'class' => Subcategory::class,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please select subcategory'
                    ])
                ],
                'placeholder' => 'Select subcategory'
            ]);
    }
}