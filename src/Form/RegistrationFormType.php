<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Producer;
use App\Entity\User;
use App\Validator\EmailNotExists;
use Rollerworks\Component\PasswordStrength\Validator\Constraints\PasswordRequirements;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotCompromisedPassword;

class RegistrationFormType extends AbstractType
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
                    'constraints' => [
                        new NotBlank(),
                        new Email(mode: 'strict'),
                        new EmailNotExists()
                    ]
                ]
            )
            ->add(
                'plainPassword',
                RepeatedType::class,
                [
                    'type' => PasswordType::class,
                    'first_options' => [
                        'label' => 'Mot de passe',
                        'label_attr' => [
                            'class' => 'font-weight-bold'
                        ],
                        'constraints' => [
                            new NotBlank(),
                            new PasswordRequirements(options: [
                                'requireLetters' => true,
                                'requireSpecialCharacter' => true,
                                'requireNumbers' => true,
                                'requireCaseDiff' => true,
                                'minLength' => 8
                            ]),
                            new NotCompromisedPassword(
                                message: 'Ce mot de passe a été divulgué lors d\'une fuite de 
                                données sur un autre site, il ne doit plus être utilisé. 
                                Veuillez utiliser un autre mot de passe.',
                                skipOnError: true
                            )
                        ]
                    ],
                    'second_options' => [
                        'label' => 'Mot de passe (Confirmation)',
                        'label_attr' => [
                            'class' => 'font-weight-bold'
                        ],
                    ],
                    'invalid_message' => 'Les mots de passe ne correspondent pas.'
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
                    'constraints' => [
                        new NotBlank()
                    ]
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
                    'constraints' => [
                        new NotBlank()
                    ]
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
