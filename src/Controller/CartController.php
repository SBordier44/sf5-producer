<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\CartItem;
use App\Entity\Customer;
use App\Entity\Product;
use App\Handler\CartHandler;
use App\HandlerFactory\HandlerFactoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cart', name: 'cart_')]
#[IsGranted('ROLE_CUSTOMER')]
class CartController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    #[Route('/add/{id}', name: 'add')]
    #[IsGranted('add_to_cart', subject: 'product')]
    public function add(Product $product): RedirectResponse
    {
        /** @var Customer $customer */
        $customer = $this->getUser();

        if ($product->getQuantity() === 0) {
            $this->addFlash(
                'warning',
                'Le produit sélectionné n\'est plus en stock et ne peut donc pas être ajouté à votre panier'
            );
        } else {
            $customer->addToCart($product);

            $this->em->flush();

            $this->addFlash('success', 'Le produit a bien été ajouté à votre panier');
        }
        return $this->redirectToRoute(
            'farm_show',
            [
                'slug' => $product->getFarm()->getSlug()
            ]
        );
    }

    #[Route('/', name: 'index')]
    #[IsGranted('ROLE_CUSTOMER')]
    public function index(Request $request, HandlerFactoryInterface $handlerFactory): Response
    {
        $handler = $handlerFactory->createHandler(CartHandler::class);

        if ($handler->handle($request, $this->getUser())) {
            return $this->redirectToRoute('cart_index');
        }

        return $this->render(
            'ui/cart/index.html.twig',
            [
                'form' => $handler->createView()
            ]
        );
    }

    #[Route('/{id}/increase_quantity', name: 'item_increase')]
    #[IsGranted('ROLE_CUSTOMER')]
    public function increaseQuantity(CartItem $cartItem): RedirectResponse
    {
        if ($cartItem->getQuantity() >= $cartItem->getProduct()->getQuantity()) {
            $this->addFlash(
                'warning',
                "Il ne reste que {$cartItem->getProduct()->getQuantity()} 
                articles pour ce produit. Je ne pouvez pas en ajouter d'avantage"
            );
        } elseif ($cartItem->getQuantity() < 100) {
            $cartItem->increaseQuantity();

            $this->em->flush();
        }

        return $this->redirectToRoute('cart_index');
    }

    #[Route('/{id}/decrease_quantity', name: 'item_decrease')]
    #[IsGranted('ROLE_CUSTOMER')]
    public function decreaseQuantity(CartItem $cartItem): RedirectResponse
    {
        if ($cartItem->getQuantity() > 0) {
            $cartItem->decreaseQuantity();

            $this->em->flush();
        }

        return $this->redirectToRoute('cart_index');
    }

    #[Route('/{id}/remove', name: 'item_remove')]
    #[IsGranted('ROLE_CUSTOMER')]
    public function removeItem(CartItem $cartItem): RedirectResponse
    {
        $this->em->remove($cartItem);

        $this->em->flush();

        $this->addFlash('success', 'Produit retiré de votre panier avec succès.');

        return $this->redirectToRoute('cart_index');
    }
}
