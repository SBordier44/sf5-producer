<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Farm;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class FarmType extends AbstractType
{
    public function __construct(private RequestStack $requestStack)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->addEventListener(
                FormEvents::PRE_SET_DATA,
                function (FormEvent $event): void {
                    $form = $event->getForm();
                    /** @var Farm $farm */
                    $farm = $event->getData();

                    if ($farm->getId() !== null) {
                        $form
                            ->add('address', FarmAddressType::class, [
                                'label' => false
                            ])
                            ->add('siret', TextType::class, [
                                'label' => "Numéro siret de l'établissement",
                                'label_attr' => [
                                    'class' => 'font-weight-bold'
                                ],
                                'mapped' => false,
                                'required' => false,
                                'disabled' => true,
                                'data' => $this->requestStack->getSession()->get('stepOne')?->getSiret()
                            ])
                            ->add(
                                'description',
                                TextareaType::class,
                                [
                                    'label' => 'Présentation de votre établissement',
                                    'label_attr' => [
                                        'class' => 'font-weight-bold'
                                    ],
                                    'constraints' => [
                                        new NotBlank()
                                    ]
                                ]
                            );
                    }
                }
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', Farm::class);
    }
}
