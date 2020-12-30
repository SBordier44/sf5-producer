<?php

declare(strict_types=1);

namespace App\Form;

use App\Dto\ForgottenPasswordInput;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ForgottenPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('email', EmailType::class, [
            'label_attr' => [
                'class' => 'font-weight-bold'
            ],
            'label' => 'Votre Email',
            'empty_data' => ''
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', ForgottenPasswordInput::class);
    }
}
