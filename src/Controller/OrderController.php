<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\CartItem;
use App\Entity\Customer;
use App\Entity\Order;
use App\Entity\OrderLine;
use App\Entity\Producer;
use App\Entity\Product;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Security\Voter\OrderVoter;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Workflow\WorkflowInterface;

#[Route('/order', name: 'order_')]
class OrderController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    #[Route('/create', name: 'create')]
    #[IsGranted('ROLE_CUSTOMER')]
    public function create(): RedirectResponse
    {
        /** @var Customer $customer */
        $customer = $this->getUser();

        foreach ($customer->getCart() as $cartItem) {
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
            ->setCustomer($customer)
            ->setFarm($customer->getCart()->first()->getProduct()->getFarm());

        /** @var CartItem $cartItem */
        foreach ($customer->getCart() as $cartItem) {
            $line = (new OrderLine())
                ->setOrder($order)
                ->setQuantity($cartItem->getQuantity())
                ->setProduct($cartItem->getProduct())
                ->setPrice($cartItem->getProduct()->getPrice());

            $order->getLines()->add($line);

            $product = $this->em->getRepository(Product::class)->find($cartItem->getProduct()->getId());

            if ($product) {
                $product->setQuantity($product->getQuantity() - $cartItem->getQuantity());

                $this->em->persist($product);
            }
        }

        $customer->getCart()->clear();

        $this->em->persist($order);

        $this->em->flush();

        return $this->redirectToRoute('order_history');
    }

    #[Route('/history', name: 'history')]
    #[IsGranted('ROLE_CUSTOMER')]
    public function history(Request $request, OrderRepository $orderRepository, PaginatorInterface $paginator): Response
    {
        /** @var Customer $customer */
        $customer = $this->getUser();

        $orders = $paginator->paginate(
            $orderRepository->findByCustomerOrdered($customer),
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

    #[Route('/manage', name: 'manage')]
    #[IsGranted('ROLE_PRODUCER')]
    public function manage(Request $request, OrderRepository $orderRepository, PaginatorInterface $paginator): Response
    {
        /** @var Producer $producer */
        $producer = $this->getUser();

        $orders = $paginator->paginate(
            $orderRepository->findBy(
                [
                    'farm' => $producer->getFarm()
                ],
                [
                    'orderReference' => 'asc'
                ]
            ),
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

    #[Route('/{id}/cancel', name: 'cancel')]
    #[IsGranted('cancel', subject: 'order')]
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

                $this->em->persist($product);
            }
        }
        $this->em->flush();

        return $this->redirectToRoute('order_history');
    }

    #[Route('/{id}/refuse', name: 'refuse')]
    #[IsGranted('refuse', subject: 'order')]
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

                $this->em->persist($product);
            }
        }

        $this->em->flush();

        return $this->redirectToRoute('order_manage');
    }

    #[Route('/{id}/accept', name: 'accept')]
    #[IsGranted('accept', subject: 'order')]
    public function accept(Order $order, WorkflowInterface $orderStateMachine): Response
    {
        $orderStateMachine->apply($order, OrderVoter::ACCEPT);

        return $this->redirectToRoute('order_manage');
    }

    #[Route('/{id}/process', name: 'process')]
    #[IsGranted('process', subject: 'order')]
    public function process(Order $order, WorkflowInterface $orderStateMachine): Response
    {
        $orderStateMachine->apply($order, OrderVoter::PROCESS);

        return $this->redirectToRoute('order_manage');
    }

    #[Route('/{id}/ready', name: 'ready')]
    #[IsGranted('ready', subject: 'order')]
    public function ready(Order $order, WorkflowInterface $orderStateMachine): Response
    {
        $orderStateMachine->apply($order, OrderVoter::READY);

        return $this->redirectToRoute('order_manage');
    }

    #[Route('/{id}/deliver', name: 'deliver')]
    #[IsGranted('deliver', subject: 'order')]
    public function deliver(Order $order, WorkflowInterface $orderStateMachine): Response
    {
        $orderStateMachine->apply($order, OrderVoter::DELIVER);

        return $this->redirectToRoute('order_manage');
    }

    #[Route('/{orderReference}/details', name: 'show')]
    #[IsGranted('ROLE_USER')]
    public function show(Order $order): Response
    {
        $this->denyAccessUnlessGranted(OrderVoter::PRODUCER_VIEW, $order);

        return $this->render(
            'ui/order/show.html.twig',
            [
                'order' => $order
            ]
        );
    }
}
