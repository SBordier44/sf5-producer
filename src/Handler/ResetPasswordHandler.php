<?php

declare(strict_types=1);

namespace App\Handler;

use App\Entity\User;
use App\Form\ResetPasswordType;
use App\HandlerFactory\AbstractHandler;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class ResetPasswordHandler
 * @package App\Handler
 */
class ResetPasswordHandler extends AbstractHandler
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
     * @var UserPasswordEncoderInterface
     */
    private UserPasswordEncoderInterface $passwordEncoder;

    public function __construct(
        EntityManagerInterface $entityManager,
        FlashBagInterface $flashBag,
        UserPasswordEncoderInterface $passwordEncoder,
        ContainerInterface $container
    ) {
        $this->entityManager = $entityManager;
        $this->flashBag = $flashBag;
        $this->passwordEncoder = $passwordEncoder;
        $this->setFormFactory($container->get('form.factory'));
    }

    protected function process($data, array $options): void
    {
        /** @var User $data */
        $data->setPassword($this->passwordEncoder->encodePassword($data, $data->getPlainPassword()));
        $this->entityManager->flush();
        $this->flashBag->add('success', 'Votre mot de passe a été mis à jour avec succès.');
    }

    protected function configure(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'form_type' => ResetPasswordType::class,
                'form_options' => [
                    'validation_groups' => [
                        'password'
                    ]
                ]
            ]
        );
    }
}
