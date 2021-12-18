<?php

declare(strict_types=1);

namespace App\Handler;

use App\Entity\CartItem;
use App\Entity\Customer;
use App\Form\CartType;
use App\HandlerFactory\AbstractHandler;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CartHandler extends AbstractHandler
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
        /** @var Customer $data */
        foreach ($data->getCart() as $cartItem) {
            /** @var CartItem $cartItem */
            if ($cartItem->getProduct()->getQuantity() === 0) {
                $this->flashBag->add(
                    'warning',
                    "<i class='fas fa-exclamation-circle mr-2'></i>Le produit 
                    <strong>{$cartItem->getProduct()->getName()}</strong> dans votre panier 
                    n'est actuellement plus en stock. <br>
                    Veuillez le retirer afin de pouvoir continuer votre commande."
                );

                return;
            }

            if ($cartItem->getProduct()->getQuantity() < $cartItem->getQuantity()) {
                $this->flashBag->add(
                    'danger',
                    "<i class='fas fa-exclamation-triangle mr-2'></i>Le produit 
                    <strong>{$cartItem->getProduct()->getName()}</strong> a 
                    une quantité limitée à <strong>{$cartItem->getProduct()->getQuantity()}</strong>. <br> 
                    Il est impossible de mettre une quantité supérieure."
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
