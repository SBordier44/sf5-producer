<?php

declare(strict_types=1);

namespace App\Handler;

use App\Entity\CartItem;
use App\Entity\Customer;
use App\Form\CartType;
use App\HandlerFactory\AbstractHandler;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CartHandler
 * @package App\Handler
 */
class CartHandler extends AbstractHandler
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
        /** @var Customer $data */
        foreach ($data->getCart() as $cartItem) {
            /** @var CartItem $cartItem */
            if ($cartItem->getProduct()->getQuantity() === 0) {
                $this->flashBag->add(
                    'warning',
                    "Le produit <strong>{$cartItem->getProduct()->getName()}</strong> dans votre panier 
                    n'est actuellement plus en stock. 
                    Veuillez le retirer afin de pouvoir continuer votre commande."
                );
                return;
            }

            if ($cartItem->getProduct()->getQuantity() < $cartItem->getQuantity()) {
                $this->flashBag->add(
                    'warning',
                    "Le produit <strong>{$cartItem->getProduct()->getName()}</strong> a 
                    une quantité limitée à <strong>{$cartItem->getProduct()->getQuantity()}</strong> 
                    Veuillez modifier la quantité désirée afin de continuer la commande."
                );
                return;
            }
        }
        $this->entityManager->flush();
        $this->flashBag->add('success', 'Votre panier a été mis à jour avec succès.');
    }

    protected function configure(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'form_type' => CartType::class
            ]
        );
    }
}
