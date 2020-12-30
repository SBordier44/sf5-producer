<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserInfoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'lastName',
                TextType::class,
                [
                    'label' => 'Votre Nom',
                    'label_attr' => [
                        'class' => 'font-weight-bold'
                    ],
                    'empty_data' => ''
                ]
            )
            ->add(
                'firstName',
                TextType::class,
                [
                    'label' => 'Votre Prénom',
                    'label_attr' => [
                        'class' => 'font-weight-bold'
                    ],
                    'empty_data' => ''
                ]
            )
            ->add(
                'email',
                EmailType::class,
                [
                    'label' => 'Adresse Email',
                    'label_attr' => [
                        'class' => 'font-weight-bold'
                    ],
                    'empty_data' => ''
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', User::class);
    }
}
