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
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('address1', TextType::class)
            ->add('address2', TextType::class, ['required' => false])
            ->add('address3', TextType::class, ['required' => false])
            ->add('zipCode', TextType::class)
            ->add('city', TextType::class)
            ->add('region', TextType::class)
            ->add('country', TextType::class)
            ->add('position', PositionType::class, ['label' => false])
            ->add('phone', TextType::class)
            ->add('phone2', TextType::class, ['required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('data_class', Address::class);
    }
}
