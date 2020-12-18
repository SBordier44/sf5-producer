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
            ->add(
                'address1',
                TextType::class,
                [
                    'label' => 'Adresse',
                    'empty_data' => ''
                ]
            )
            ->add('address2', TextType::class, ['required' => false, 'label' => 'Adresse (suite)'])
            ->add('address3', TextType::class, ['required' => false, 'label' => 'Adresse (suite)'])
            ->add(
                'zipCode',
                TextType::class,
                [
                    'label' => 'Code postal',
                    'empty_data' => ''
                ]
            )
            ->add(
                'city',
                TextType::class,
                [
                    'label' => 'Ville',
                    'empty_data' => ''
                ]
            )
            ->add(
                'region',
                TextType::class,
                [
                    'label' => 'Département / Région',
                    'empty_data' => ''
                ]
            )
            ->add(
                'country',
                TextType::class,
                [
                    'label' => 'Pays',
                    'empty_data' => ''
                ]
            )
            ->add('position', PositionType::class, ['label' => false])
            ->add(
                'phone',
                TextType::class,
                [
                    'label' => 'Téléphone',
                    'empty_data' => ''
                ]
            )
            ->add('phone2', TextType::class, ['required' => false, 'label' => 'Téléphone (autre)']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('data_class', Address::class);
    }
}
