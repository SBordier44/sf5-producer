<?php

namespace App\Form;

use App\Dto\FacilitySiretDto;
use App\Validator\SiretNotExists;
use App\Validator\SiretValid;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class SiretType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('siret', TextType::class, [
            'label' => 'Numéro Siret de votre établissement',
            'label_attr' => [
                'class' => 'font-weight-bold'
            ],
            'constraints' => [
                new NotBlank(),
                new SiretValid(),
                new Length(exactly: 14),
                new SiretNotExists()
            ]
        ]);

        $builder->add('submit', SubmitType::class, [
            'label' => 'Valider',
            'attr' => [
                'class' => 'btn btn-primary'
            ]
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FacilitySiretDto::class
        ]);
    }
}
