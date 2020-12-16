<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\CartItem;
use App\Entity\Order;
use App\Entity\OrderLine;
use App\Repository\OrderRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

/**
 * Class OrderController
 * @package App\Controller
 * @Route("/order")
 */
class OrderController extends AbstractController
{
    /**
     * @return RedirectResponse
     * @Route("/create", name="order_create")
     * @IsGranted("ROLE_CUSTOMER")
     */
    public function create(): RedirectResponse
    {
        $order = (new Order())
            ->setId(Uuid::v4())
            ->setCustomer($this->getUser());

        /** @var CartItem $cartItem */
        foreach ($this->getUser()->getCart() as $cartItem) {
            $line = (new OrderLine())
                ->setId(Uuid::v4())
                ->setOrder($order)
                ->setQuantity($cartItem->getQuantity())
                ->setProduct($cartItem->getProduct())
                ->setPrice($cartItem->getProduct()->getPrice());
            $order->getLines()->add($line);
        }

        $this->getUser()->getCart()->clear();
        $this->getDoctrine()->getManager()->persist($order);
        $this->getDoctrine()->getManager()->flush();
        return $this->redirectToRoute('index');
    }

    /**
     * @param OrderRepository $orderRepository
     * @return Response
     * @Route("/history", name="order_history")
     * @IsGranted("ROLE_CUSTOMER")
     */
    public function history(OrderRepository $orderRepository): Response
    {
        return $this->render(
            'ui/order/history.html.twig',
            [
                'orders' => $orderRepository->findByCustomer($this->getUser())
            ]
        );
    }

    /**
     * @param Order $order
     * @return RedirectResponse
     * @Route("/{id}/cancel", name="order_cancel")
     * @IsGranted("cancel", subject="order")
     */
    public function cancel(Order $order): RedirectResponse
    {
        $order->setState('cancelled');
        $order->setCanceledAt(new \DateTimeImmutable());
        $this->getDoctrine()->getManager()->flush();
        return $this->redirectToRoute('order_history');
    }
}
