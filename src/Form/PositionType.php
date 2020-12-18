<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Position;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PositionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'latitude',
                HiddenType::class,
                [
                    'label' => false,
                    'empty_data' => ''
                ]
            )
            ->add(
                'longitude',
                HiddenType::class,
                [
                    'label' => false,
                    'empty_data' => ''
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('data_class', Position::class);
    }
}
