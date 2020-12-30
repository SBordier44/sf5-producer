<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'address',
                TextType::class,
                [
                    'label' => 'Adresse',
                    'label_attr' => [
                        'class' => 'font-weight-bold'
                    ],
                    'empty_data' => ''
                ]
            )
            ->add(
                'addressExtra',
                TextType::class,
                [
                    'required' => false,
                    'label' => 'Complément d\'adresse',
                    'label_attr' => [
                        'class' => 'font-weight-bold'
                    ],
                    'empty_data' => ''
                ]
            )
            ->add(
                'zipCode',
                TextType::class,
                [
                    'label' => 'Code postal',
                    'label_attr' => [
                        'class' => 'font-weight-bold'
                    ],
                    'empty_data' => ''
                ]
            )
            ->add(
                'city',
                TextType::class,
                [
                    'label' => 'Ville',
                    'label_attr' => [
                        'class' => 'font-weight-bold'
                    ],
                    'empty_data' => ''
                ]
            )
            ->add(
                'region',
                TextType::class,
                [
                    'label' => 'Département / Région',
                    'label_attr' => [
                        'class' => 'font-weight-bold'
                    ],
                    'empty_data' => ''
                ]
            )
            ->add(
                'country',
                TextType::class,
                [
                    'label' => 'Pays',
                    'label_attr' => [
                        'class' => 'font-weight-bold'
                    ],
                    'empty_data' => ''
                ]
            )
            ->add(
                'position',
                PositionType::class,
                [
                    'label' => false
                ]
            )
            ->add(
                'phone',
                TextType::class,
                [
                    'label' => 'Téléphone',
                    'label_attr' => [
                        'class' => 'font-weight-bold'
                    ],
                    'empty_data' => ''
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', Address::class);
    }
}
