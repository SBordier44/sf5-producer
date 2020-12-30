<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\CartItem;
use App\Entity\Order;
use App\Entity\OrderLine;
use App\Entity\Product;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Security\Voter\OrderVoter;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Workflow\WorkflowInterface;

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
        foreach ($this->getUser()->getCart() as $cartItem) {
            /** @var CartItem $cartItem */
            if ($cartItem->getProduct()->getQuantity() === 0) {
                $this->addFlash(
                    'warning',
                    "Le produit <strong>{$cartItem->getProduct()->getName()}</strong> dans votre panier 
                    n'est actuellement plus en stock. 
                    Veuillez le retirer afin de pouvoir continuer votre commande."
                );
                return $this->redirectToRoute('cart_index');
            }

            if ($cartItem->getProduct()->getQuantity() < $cartItem->getQuantity()) {
                $this->addFlash(
                    'warning',
                    "Le produit <strong>{$cartItem->getProduct()->getName()}</strong> a 
                    une quantité limitée à <strong>{$cartItem->getProduct()->getQuantity()}</strong>. 
                    Veuillez modifier la quantité désirée afin de continuer la commande."
                );
                return $this->redirectToRoute('cart_index');
            }
        }
        $order = (new Order())
            ->setCustomer($this->getUser())
            ->setFarm($this->getUser()->getCart()->first()->getProduct()->getFarm());

        /** @var CartItem $cartItem */
        foreach ($this->getUser()->getCart() as $cartItem) {
            $line = (new OrderLine())
                ->setOrder($order)
                ->setQuantity($cartItem->getQuantity())
                ->setProduct($cartItem->getProduct())
                ->setPrice($cartItem->getProduct()->getPrice());

            $order->getLines()->add($line);
            $product = $this->getDoctrine()->getRepository(Product::class)->find($cartItem->getProduct()->getId());
            $product->setQuantity($product->getQuantity() - $cartItem->getQuantity());
            $this->getDoctrine()->getManager()->persist($product);
        }

        $this->getUser()->getCart()->clear();

        $this->getDoctrine()->getManager()->persist($order);
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('order_history');
    }

    /**
     * @param Request $request
     * @param OrderRepository $orderRepository
     * @param PaginatorInterface $paginator
     * @return Response
     * @Route("/history", name="order_history")
     * @IsGranted("ROLE_CUSTOMER")
     */
    public function history(Request $request, OrderRepository $orderRepository, PaginatorInterface $paginator): Response
    {
        $orders = $paginator->paginate(
            $orderRepository->findByCustomerOrdered($this->getUser()),
            $request->query->getInt('page', 1)
        );
        $orders->setCustomParameters(
            [
                'align' => 'center',
                'size' => 'small',
                'rounded' => true
            ]
        );
        return $this->render(
            'ui/order/history.html.twig',
            [
                'orders' => $orders
            ]
        );
    }

    /**
     * @param Request $request
     * @param OrderRepository $orderRepository
     * @param PaginatorInterface $paginator
     * @return Response
     * @Route("/manage", name="order_manage")
     * @IsGranted("ROLE_PRODUCER")
     */
    public function manage(Request $request, OrderRepository $orderRepository, PaginatorInterface $paginator): Response
    {
        $orders = $paginator->paginate(
            $orderRepository->findByFarm($this->getUser()->getFarm()),
            $request->query->getInt('page', 1)
        );
        $orders->setCustomParameters(
            [
                'align' => 'center',
                'size' => 'small',
                'rounded' => true
            ]
        );
        return $this->render(
            'ui/order/manage.html.twig',
            [
                'orders' => $orders
            ]
        );
    }

    /**
     * @param Order $order
     * @param WorkflowInterface $orderStateMachine
     * @param ProductRepository $productRepository
     * @return RedirectResponse
     * @Route("/{id}/cancel", name="order_cancel")
     * @IsGranted("cancel", subject="order")
     */
    public function cancel(
        Order $order,
        WorkflowInterface $orderStateMachine,
        ProductRepository $productRepository
    ): RedirectResponse {
        $orderStateMachine->apply($order, OrderVoter::CANCEL);
        foreach ($order->getLines() as $line) {
            /** @var OrderLine $line */
            $product = $productRepository->find($line->getProduct()->getId());
            if ($product) {
                $product->setQuantity($product->getQuantity() + $line->getQuantity());
                $this->getDoctrine()->getManager()->persist($product);
            }
        }
        $this->getDoctrine()->getManager()->flush();
        return $this->redirectToRoute('order_history');
    }

    /**
     * @param Order $order
     * @param WorkflowInterface $orderStateMachine
     * @param ProductRepository $productRepository
     * @return RedirectResponse
     * @Route("/{id}/refuse", name="order_refuse")
     * @IsGranted("refuse", subject="order")
     */
    public function refuse(
        Order $order,
        WorkflowInterface $orderStateMachine,
        ProductRepository $productRepository
    ): RedirectResponse {
        $orderStateMachine->apply($order, OrderVoter::REFUSE);
        foreach ($order->getLines() as $line) {
            /** @var OrderLine $line */
            $product = $productRepository->find($line->getProduct()->getId());
            if ($product) {
                $product->setQuantity($product->getQuantity() + $line->getQuantity());
                $this->getDoctrine()->getManager()->persist($product);
            }
        }
        $this->getDoctrine()->getManager()->flush();
        return $this->redirectToRoute('order_manage');
    }

    /**
     * @param Order $order
     * @param WorkflowInterface $orderStateMachine
     * @return RedirectResponse
     * @Route("/{id}/accept", name="order_accept")
     * @IsGranted("accept", subject="order")
     */
    public function accept(Order $order, WorkflowInterface $orderStateMachine): Response
    {
        $orderStateMachine->apply($order, OrderVoter::ACCEPT);
        return $this->redirectToRoute('order_manage');
    }

    /**
     * @param Order $order
     * @param WorkflowInterface $orderStateMachine
     * @return RedirectResponse
     * @Route("/{id}/process", name="order_process")
     * @IsGranted("process", subject="order")
     */
    public function process(Order $order, WorkflowInterface $orderStateMachine): Response
    {
        $orderStateMachine->apply($order, OrderVoter::PROCESS);
        return $this->redirectToRoute('order_manage');
    }

    /**
     * @param Order $order
     * @param WorkflowInterface $orderStateMachine
     * @return RedirectResponse
     * @Route("/{id}/ready", name="order_ready")
     * @IsGranted("ready", subject="order")
     */
    public function ready(Order $order, WorkflowInterface $orderStateMachine): Response
    {
        $orderStateMachine->apply($order, OrderVoter::READY);
        return $this->redirectToRoute('order_manage');
    }

    /**
     * @param Order $order
     * @param WorkflowInterface $orderStateMachine
     * @return RedirectResponse
     * @Route("/{id}/deliver", name="order_deliver")
     * @IsGranted("deliver", subject="order")
     */
    public function deliver(Order $order, WorkflowInterface $orderStateMachine): Response
    {
        $orderStateMachine->apply($order, OrderVoter::DELIVER);
        return $this->redirectToRoute('order_manage');
    }

    /**
     * @param Order $order
     * @return Response
     * @Route("/{orderReference}/details", name="order_show")
     * @IsGranted("ROLE_USER")
     */
    public function show(Order $order): Response
    {
        return $this->render(
            'ui/order/show.html.twig',
            [
                'order' => $order
            ]
        );
    }
}
