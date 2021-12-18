<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\User;
use Rollerworks\Component\PasswordStrength\Validator\Constraints\PasswordRequirements;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotCompromisedPassword;

class UserPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'currentPassword',
                PasswordType::class,
                [
                    'mapped' => false,
                    'required' => true,
                    'constraints' => [
                        new UserPassword(
                            [
                                'message' => 'Le mot de passe saisi n\'est pas celui utilisé actuellement.'
                            ]
                        )
                    ],
                    'label' => 'Votre Mot de passe actuel',
                    'label_attr' => [
                        'class' => 'font-weight-bold'
                    ],
                ]
            )
            ->add(
                'plainPassword',
                RepeatedType::class,
                [
                    'type' => PasswordType::class,
                    'required' => true,
                    'first_options' => [
                        'label' => 'Nouveau mot de passe',
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
                        'label' => 'Nouveau mot de passe (Confirmation)',
                        'label_attr' => [
                            'class' => 'font-weight-bold'
                        ],
                    ],
                    'invalid_message' => 'Les mots de passe ne correspondent pas.'
                ]
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
