<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\CartItem;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;

class CartItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('quantity', NumberType::class, [
            'html5' => false,
            'empty_data' => 0,
            'required' => true,
            'label' => false,
            'attr' => [
                'class' => 'form-control-sm border-0 shadow-0 p-0 input-qty'
            ],
            'constraints' => [
                new NotBlank(),
                new GreaterThanOrEqual(value: 0)
            ]
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', CartItem::class);
    }
}
