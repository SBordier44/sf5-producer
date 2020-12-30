<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Image;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'path',
                HiddenType::class,
                [
                    'required' => false
                ]
            )
            ->add(
                'file',
                FileType::class,
                [
                    'label' => 'Image',
                    'label_attr' => [
                        'class' => 'font-weight-bold'
                    ],
                    'attr' => [
                        'class' => 'custom-file-input'
                    ],
                    'required' => false
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', Image::class);
    }
}
