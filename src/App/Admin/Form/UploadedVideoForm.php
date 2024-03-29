<?php

namespace App\Admin\Form;

use App\Domain\Entity\UploadedVideo;
use ExtrasBundle\Utils\UuidGenerator;
use Sonata\Form\Type\DateTimePickerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class UploadedVideoForm
 */
class UploadedVideoForm extends PreUploadForm
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('title', TextType::class)
            ->add('description', TextType::class)
            ->add('expiredDate', DateTimePickerType::class, [
                'format' => 'Y-MM-dd HH:mm'
            ])
            ->add('remoteId', TextType::class)
            ->add('remoteUrl', TextType::class)
            ->add('thumbnails');
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'presets' => [],
            'data_class' => UploadedVideo::class,
            'empty_data' => function (FormInterface $form) {
                return new UploadedVideo(UuidGenerator::generate());
            },
            'csrf_protection' => false
        ]);
    }
}