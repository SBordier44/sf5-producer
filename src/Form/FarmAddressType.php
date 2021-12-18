<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Valid;

class FarmAddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'phone',
                TextType::class,
                [
                    'label' => 'Téléphone',
                    'label_attr' => [
                        'class' => 'font-weight-bold'
                    ],
                    'constraints' => [
                        new NotBlank(),
                        new Regex(pattern: "/^[0-9]{10}$/", message: 'Numéro de téléphone invalide.')
                    ]
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', Address::class);
    }
}
