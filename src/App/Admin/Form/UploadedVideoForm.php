<?php

namespace App\Admin\Form;

use App\Domain\Entity\UploadedVideo;
use App\Utils\UuidGenerator;
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
            ->add('description', TextType::class);
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
            }
        ]);
    }
}