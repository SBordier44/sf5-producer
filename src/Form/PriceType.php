<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Price;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PriceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'unitPrice',
                MoneyType::class,
                [
                    'scale' => 0,
                    'label' => 'Prix unitaire HT',
                    'label_attr' => [
                        'class' => 'font-weight-bold'
                    ],
                    'empty_data' => 0
                ]
            )
            ->add(
                'vat',
                ChoiceType::class,
                [
                    'choices' => [
                        '0%' => 0.0,
                        '2.1%' => 2.1,
                        '5.5%' => 5.5,
                        '10%' => 10.0,
                        '20%' => 20.0
                    ],
                    'label' => 'TVA',
                    'label_attr' => [
                        'class' => 'font-weight-bold'
                    ],
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', Price::class);
    }
}
