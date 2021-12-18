<?php

declare(strict_types=1);

namespace App\Handler;

use App\Entity\User;
use App\Form\UserPasswordType;
use App\HandlerFactory\AbstractHandler;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserPasswordHandler extends AbstractHandler
{
    #[Pure]
    public function __construct(
        private EntityManagerInterface $entityManager,
        private FlashBagInterface $flashBag,
        private UserPasswordHasherInterface $passwordHasher,
        FormFactoryInterface $formFactory
    ) {
        parent::__construct($formFactory);
    }

    protected function process($data, array $options): void
    {
        /** @var User $data */
        $data->setPassword($this->passwordHasher->hashPassword($data, $data->getPlainPassword()));

        $this->entityManager->flush();

        $this->flashBag->add('success', 'Votre mot de passe a été modifié avec succès.');
    }

    protected function configure(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'form_type' => UserPasswordType::class
            ]
        );
    }
}
