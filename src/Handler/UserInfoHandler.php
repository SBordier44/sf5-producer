<?php

declare(strict_types=1);

namespace App\Handler;

use App\Form\UserInfoType;
use App\HandlerFactory\AbstractHandler;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class UserInfoHandler
 * @package App\Handler
 */
class UserInfoHandler extends AbstractHandler
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;
    /**
     * @var FlashBagInterface
     */
    private FlashBagInterface $flashBag;

    public function __construct(
        EntityManagerInterface $entityManager,
        FlashBagInterface $flashBag,
        ContainerInterface $container
    ) {
        $this->entityManager = $entityManager;
        $this->flashBag = $flashBag;
        $this->setFormFactory($container->get('form.factory'));
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
