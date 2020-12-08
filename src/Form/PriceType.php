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
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('unitPrice', MoneyType::class, ['scale' => 0])
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
                    ]
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('data_class', Price::class);
    }
}
