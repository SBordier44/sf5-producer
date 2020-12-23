<?php

declare(strict_types=1);

namespace App\Handler;

use App\Entity\User;
use App\Form\ForgottenPasswordType;
use App\HandlerFactory\AbstractHandler;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ForgottenPasswordHandler
 * @package App\Handler
 */
class ForgottenPasswordHandler extends AbstractHandler
{

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @var FlashBagInterface
     */
    private FlashBagInterface $flashBag;
    /**
     * @var MailerInterface
     */
    private MailerInterface $mailer;
    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        FlashBagInterface $flashBag,
        MailerInterface $mailer,
        UserRepository $userRepository,
        ContainerInterface $container
    ) {
        $this->entityManager = $entityManager;
        $this->flashBag = $flashBag;
        $this->mailer = $mailer;
        $this->userRepository = $userRepository;
        $this->setFormFactory($container->get('form.factory'));
    }

    protected function process($data, array $options): void
    {
        /** @var User $data */
        /** @var User $user */
        $user = $this->userRepository->findOneByEmail($data->getEmail());
        $user->hasForgotHisPassword();
        $this->entityManager->flush();
        $email = (new TemplatedEmail())
            ->to(new Address($user->getEmail(), $user->getFullName()))
            ->from(new Address('hello@producteurauconsommateur.com'))
            ->context(['forgottenPassword' => $user->getForgottenPassword()])
            ->htmlTemplate('emails/forgotten_password.html.twig');
        $this->mailer->send($email);
        $this->flashBag->add(
            'success',
            'Votre demande a été enregistré. Vous allez recevoir, dans les prochaines minutes,
             un email vous permettant de réinitialiser votre mot de passe.'
        );
    }

    protected function configure(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'form_type' => ForgottenPasswordType::class
            ]
        );
    }
}
