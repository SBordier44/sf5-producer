<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\CartItem;
use App\Entity\Order;
use App\Entity\OrderLine;
use App\Handler\AcceptOrderHandler;
use App\HandlerFactory\HandlerFactoryInterface;
use App\Repository\OrderRepository;
use App\Security\Voter\OrderVoter;
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
        }

        $this->getUser()->getCart()->clear();

        $this->getDoctrine()->getManager()->persist($order);
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('order_history');
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
     * @param OrderRepository $orderRepository
     * @return Response
     * @Route("/manage", name="order_manage")
     * @IsGranted("ROLE_PRODUCER")
     */
    public function manage(OrderRepository $orderRepository): Response
    {
        return $this->render(
            'ui/order/manage.html.twig',
            [
                'orders' => $orderRepository->findByFarm($this->getUser()->getFarm())
            ]
        );
    }

    /**
     * @param Order $order
     * @param WorkflowInterface $orderStateMachine
     * @return RedirectResponse
     * @Route("/{id}/cancel", name="order_cancel")
     * @IsGranted("cancel", subject="order")
     */
    public function cancel(Order $order, WorkflowInterface $orderStateMachine): RedirectResponse
    {
        $orderStateMachine->apply($order, OrderVoter::CANCEL);
        return $this->redirectToRoute('order_history');
    }

    /**
     * @param Order $order
     * @param WorkflowInterface $orderStateMachine
     * @return RedirectResponse
     * @Route("/{id}/refuse", name="order_refuse")
     * @IsGranted("refuse", subject="order")
     */
    public function refuse(Order $order, WorkflowInterface $orderStateMachine): RedirectResponse
    {
        $orderStateMachine->apply($order, OrderVoter::REFUSE);
        return $this->redirectToRoute('order_manage');
    }

    /**
     * @param Order $order
     * @param WorkflowInterface $orderStateMachine
     * @return RedirectResponse
     * @Route("/{id}/settle", name="order_settle")
     * @IsGranted("settle", subject="order")
     */
    public function settle(Order $order, WorkflowInterface $orderStateMachine): RedirectResponse
    {
        $orderStateMachine->apply($order, OrderVoter::SETTLE);
        return $this->redirectToRoute('order_manage');
    }

    /**
     * @param Request $request
     * @param Order $order
     * @param HandlerFactoryInterface $handlerFactory
     * @return RedirectResponse
     * @Route("/{id}/accept", name="order_accept")
     * @IsGranted("accept", subject="order")
     */
    public function accept(Request $request, Order $order, HandlerFactoryInterface $handlerFactory): Response
    {
        $handler = $handlerFactory->createHandler(AcceptOrderHandler::class);

        if ($handler->handle($request, $order)) {
            return $this->redirectToRoute('order_manage');
        }

        return $this->render(
            'ui/order/accept.html.twig',
            [
                'form' => $handler->createView()
            ]
        );
    }
}
