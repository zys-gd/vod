<?php

namespace App\Form;

use App\Domain\Entity\VideoCategory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UploadVideoForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('category', EntityType::class, ['class' => VideoCategory::class, 'choice_label' => 'title'])
            ->add('file', FileType::class, array('label' => 'File'));

    }

    public function configureOptions(OptionsResolver $resolver)
    {

    }
}
