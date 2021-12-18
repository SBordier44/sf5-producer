<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
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
                    'label' => 'Nom du produit',
                    'label_attr' => [
                        'class' => 'font-weight-bold'
                    ],
                    'constraints' => [
                        new NotBlank()
                    ]
                ]
            )
            ->add(
                'description',
                TextType::class,
                [
                    'label' => 'Description du produit',
                    'label_attr' => [
                        'class' => 'font-weight-bold'
                    ],
                    'constraints' => [
                        new NotBlank()
                    ]
                ]
            )
            ->add(
                'price',
                PriceType::class,
                [
                    'label' => false
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', Product::class);
    }
}
