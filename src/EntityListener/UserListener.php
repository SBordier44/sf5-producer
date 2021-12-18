<?php

declare(strict_types=1);

namespace App\EntityListener;

use App\Entity\User;
use App\Security\EmailVerifier;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserListener
{
    public function __construct(
        private EmailVerifier $emailVerifier,
        private TokenStorageInterface $tokenStorage,
        private FlashBagInterface $flashBag
    ) {
    }

    public function preUpdate(User $user, PreUpdateEventArgs $eventArgs): void
    {
        /** @var User $editedUser */
        $editedUser = $eventArgs->getObject();

        if ($eventArgs->hasChangedField('email')) {
            $editedUser->setIsVerified(false);

            $this->emailVerifier->sendEmailConfirmation(
                'security_verify_email',
                $editedUser,
                (new TemplatedEmail())
                    ->from(new Address('hello@mon-petit-producteur.fr', 'Mon Petit Producteur'))
                    ->to(new Address($editedUser->getEmail(), $editedUser->getFullName()))
                    ->subject('Veuillez confirmer votre adresse Email')
                    ->htmlTemplate('ui/registration/confirmation_email.html.twig')
            );

            $this->flashBag->add(
                'info',
                'Un email viens de vous être envoyé sur votre nouvelle adresse afin de réactiver votre compte.'
            );

            $this->tokenStorage->setToken();
        }
    }
}
