<?php

declare(strict_types=1);

namespace App\Handler;

use App\Form\UserInfoType;
use App\HandlerFactory\AbstractHandler;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserInfoHandler extends AbstractHandler
{
    #[Pure]
    public function __construct(
        private EntityManagerInterface $entityManager,
        private FlashBagInterface $flashBag,
        FormFactoryInterface $formFactory
    ) {
        parent::__construct($formFactory);
    }

    protected function process($data, array $options): void
    {
        $this->entityManager->flush();

        $this->flashBag->add('success', 'Vos informations personnelles ont étés modifiées avec succès.');
    }

    protected function configure(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'form_type' => UserInfoType::class
            ]
        );
    }
}
