<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResetPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'plainPassword',
            RepeatedType::class,
            [
                'type' => PasswordType::class,
                'required' => true,
                'first_options' => [
                    'label' => 'Nouveau Mot de passe',
                    'label_attr' => [
                        'class' => 'font-weight-bold'
                    ],
                ],
                'second_options' => [
                    'label' => 'Nouveau Mot de passe (Confirmation)',
                    'label_attr' => [
                        'class' => 'font-weight-bold'
                    ],
                ],
                'invalid_message' => 'Les mots de passe ne correspondent pas.',
                'empty_data' => ''
            ]
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', User::class);
    }
}
