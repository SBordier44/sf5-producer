<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Producer;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'email',
                EmailType::class,
                [
                    'label' => 'Votre adresse email',
                    'label_attr' => [
                        'class' => 'font-weight-bold'
                    ],
                    'empty_data' => ''
                ]
            )
            ->add(
                'plainPassword',
                RepeatedType::class,
                [
                    'type' => PasswordType::class,
                    'required' => true,
                    'first_options' => [
                        'label' => 'Mot de passe',
                        'label_attr' => [
                            'class' => 'font-weight-bold'
                        ],
                    ],
                    'second_options' => [
                        'label' => 'Mot de passe (Confirmation)',
                        'label_attr' => [
                            'class' => 'font-weight-bold'
                        ],
                    ],
                    'invalid_message' => 'Les mots de passe ne correspondent pas.',
                    'empty_data' => ''
                ]
            )
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
            ->addEventListener(
                FormEvents::PRE_SET_DATA,
                function (FormEvent $event): void {
                    $user = $event->getData();

                    if (!$user instanceof Producer) {
                        return;
                    }

                    $event->getForm()->add('farm', FarmType::class, ['label' => false]);
                }
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => User::class
            ]
        );
    }
}
