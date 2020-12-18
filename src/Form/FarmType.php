<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Farm;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FarmType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'image',
                ImageType::class,
                [
                    'label' => false
                ]
            )
            ->add(
                'name',
                TextType::class,
                [
                    'label' => 'Nom de l\'exploitation',
                    'empty_data' => ''
                ]
            )
            ->add('address', AddressType::class, ['label' => false])
            ->add(
                'description',
                TextareaType::class,
                [
                    'label' => 'Description de l\'exploitation',
                    'empty_data' => ''
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('data_class', Farm::class);
    }
}
